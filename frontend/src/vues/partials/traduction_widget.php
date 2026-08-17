<?php
/**
 * Widget de traduction du site (fr/en/it/pt/ga). Le choix de langue est mémorisé via un cookie,
 * persistant sur toutes les pages. La traduction passe par l'API MyMemory : les textes de la
 * page sont traduits puis remplacés directement dans le DOM. À inclure une seule fois par page.
 *
 * Optimisations :
 * - les occurrences identiques d'un même texte ne sont traduites qu'une seule fois ;
 * - les textes sans aucune lettre ne sont pas envoyés à l'API ;
 * - les requêtes tournent avec un pool à concurrence constante ;
 * - les traductions déjà vues sont mises en cache dans le navigateur.
 */
?>
<script>
(function () {
    function lireCookie(nom) {
        var m = document.cookie.match(new RegExp('(?:^|; )' + nom + '=([^;]*)'));
        return m ? decodeURIComponent(m[1]) : null;
    }

    var langueActuelle = lireCookie('site_lang') || 'fr';

    window.changerLangueSite = function (code) {
        var expire = new Date();
        expire.setFullYear(expire.getFullYear() + 1);
        document.cookie = 'site_lang=' + code + '; path=/; expires=' + expire.toUTCString();
        window.location.reload();
    };

    var CLE_CACHE = 'nmw_traductions';
    function chargerCache() {
        try { return JSON.parse(localStorage.getItem(CLE_CACHE)) || {}; } catch (e) { return {}; }
    }
    function sauverCache(cache) {
        try { localStorage.setItem(CLE_CACHE, JSON.stringify(cache)); } catch (e) { /* stockage indisponible : tant pis, pas bloquant */ }
    }

    function contientDesLettres(texte) {
        return /[a-zA-ZÀ-ÖØ-öø-ÿ]/.test(texte);
    }

    function traduireTexte(texte, code, cache) {
        var cle = code + '::' + texte;
        if (cache[cle]) {
            return Promise.resolve(cache[cle]);
        }
        if (!contientDesLettres(texte) || texte.length > 480) {
            return Promise.resolve(texte); // rien à traduire, ou trop long pour l'API : on garde tel quel
        }
        var url = 'https://api.mymemory.translated.net/get?q=' + encodeURIComponent(texte) + '&langpair=fr|' + code;
        return fetch(url)
            .then(function (r) { return r.json(); })
            .then(function (data) {
                var traduit = (data && data.responseData && data.responseData.translatedText) ? data.responseData.translatedText : texte;
                cache[cle] = traduit;
                return traduit;
            })
            .catch(function () { return texte; });
    }

    // Pool à concurrence constante : dès qu'une requête se termine, la suivante démarre aussitôt
    // (plus rapide que des lots figés qui attendent la requête la plus lente à chaque tour).
    function executerAvecConcurrence(taches, limite, cache) {
        return new Promise(function (resolve) {
            if (taches.length === 0) { resolve(); return; }
            var index = 0;
            var enCours = 0;
            var termine = 0;
            function suivant() {
                if (termine >= taches.length) {
                    sauverCache(cache);
                    resolve();
                    return;
                }
                while (enCours < limite && index < taches.length) {
                    var tache = taches[index++];
                    enCours++;
                    tache().finally(function () {
                        enCours--;
                        termine++;
                        if (termine % 25 === 0) sauverCache(cache);
                        suivant();
                    });
                }
            }
            suivant();
        });
    }

    function collecterNoeudsTexte(racine) {
        var noeuds = [];
        var walker = document.createTreeWalker(racine, NodeFilter.SHOW_TEXT, {
            acceptNode: function (noeud) {
                if (!noeud.nodeValue.trim()) return NodeFilter.FILTER_REJECT;
                var parent = noeud.parentElement;
                if (!parent || parent.closest('.notranslate, script, style, noscript')) return NodeFilter.FILTER_REJECT;
                return NodeFilter.FILTER_ACCEPT;
            }
        });
        var n;
        while ((n = walker.nextNode())) noeuds.push(n);
        return noeuds;
    }

    function traduirePage(code) {
        var cache = chargerCache();

        // Regroupe toutes les occurrences d'un même texte : "Modifier" ou "Terminée" peuvent
        // apparaître des dizaines de fois sur une page (tableaux), autant ne les traduire qu'une fois.
        var parTexte = {};
        function enregistrer(texte, appliquer) {
            if (!parTexte[texte]) parTexte[texte] = [];
            parTexte[texte].push(appliquer);
        }

        collecterNoeudsTexte(document.body).forEach(function (noeud) {
            var original = noeud.nodeValue;
            var texte = original.trim();
            enregistrer(texte, function (traduit) {
                noeud.nodeValue = original.replace(texte, traduit);
            });
        });

        Array.prototype.slice.call(document.querySelectorAll('[placeholder]'))
            .filter(function (el) { return !el.closest('.notranslate'); })
            .forEach(function (el) {
                var texte = el.getAttribute('placeholder');
                enregistrer(texte, function (traduit) {
                    el.setAttribute('placeholder', traduit);
                });
            });

        var taches = Object.keys(parTexte).map(function (texte) {
            return function () {
                return traduireTexte(texte, code, cache).then(function (traduit) {
                    parTexte[texte].forEach(function (appliquer) { appliquer(traduit); });
                });
            };
        });

        executerAvecConcurrence(taches, 12, cache);
    }

    document.addEventListener('DOMContentLoaded', function () {
        var noms = { fr: 'FR', en: 'EN', it: 'IT', pt: 'PT', ga: 'GA' };
        document.querySelectorAll('.langue-actuelle').forEach(function (el) {
            el.textContent = noms[langueActuelle] || 'FR';
        });

        if (langueActuelle !== 'fr') {
            traduirePage(langueActuelle);
        }
    });
})();
</script>
