-- ============================================================
-- Script d'insertion de 2 collectes de test (non validées)
-- Produits en statut 'À venir', bénévoles non confirmés
-- ============================================================

USE nomorewaste;

-- ------------------------------------------------------------
-- 1. COLLECTE N°1 : Boulangerie Dupont (commerçant ID 2)
-- ------------------------------------------------------------
INSERT INTO collecte (
    date_heure_collecte,
    adresse_collecte,
    statut,
    commentaire,
    commercant_id,
    validee,
    nb_benevoles
)
VALUES (
    '2026-08-25 07:30:00',
    '10 Rue de la Paix, 75002 Paris',
    '',  -- statut vide = en attente de validation
    'Collecte des invendus du matin (pain, viennoiseries)',
    2,   -- ID du commerçant "Boulangerie Dupont"
    0,   -- non validée
    0    -- nb_benevoles sera mis à jour après association
);

SET @collecte1_id = LAST_INSERT_ID();

-- Produits associés à la collecte 1
INSERT INTO produit (
    code_barre,
    nom,
    categorie,
    quantite,
    date_peremption,
    collecte_id,
    statut
) VALUES
    ('3760123456789', 'Pain de campagne', 'Boulangerie', 20, '2026-08-28', @collecte1_id, 'À venir'),
    ('3760123456790', 'Croissants assortis', 'Boulangerie', 30, '2026-08-26', @collecte1_id, 'À venir'),
    ('3560070460096', 'Yaourts nature x12', 'Produits laitiers', 8, '2026-08-30', @collecte1_id, 'À venir');

-- Associer 2 bénévoles (IDs 4 et 5) – ne pas confirmer
INSERT INTO collecte_benevole (collecte_id, benevole_id, confirme) VALUES
    (@collecte1_id, 4, 0),
    (@collecte1_id, 5, 0);

UPDATE collecte SET nb_benevoles = 2 WHERE id = @collecte1_id;

-- ------------------------------------------------------------
-- 2. COLLECTE N°2 : Supermarché Martin (commerçant ID 3)
-- ------------------------------------------------------------
INSERT INTO collecte (
    date_heure_collecte,
    adresse_collecte,
    statut,
    commentaire,
    commercant_id,
    validee,
    nb_benevoles
)
VALUES (
    '2026-08-27 18:00:00',
    '5 Avenue Victor Hugo, 75016 Paris',
    '',
    'Collecte de produits frais et épicerie',
    3,
    0,
    0
);

SET @collecte2_id = LAST_INSERT_ID();

-- Produits associés à la collecte 2
INSERT INTO produit (
    code_barre,
    nom,
    categorie,
    quantite,
    date_peremption,
    collecte_id,
    statut
) VALUES
    ('3017620422003', 'Confiture fraise', 'Épicerie', 15, '2026-12-01', @collecte2_id, 'À venir'),
    ('3228857000902', 'Salade mélangée', 'Frais', 12, '2026-08-29', @collecte2_id, 'À venir'),
    ('3760123456791', 'Baguettes tradition', 'Boulangerie', 25, '2026-08-28', @collecte2_id, 'À venir'),
    ('3560070460097', 'Fromage blanc x6', 'Produits laitiers', 10, '2026-09-01', @collecte2_id, 'À venir');

-- Associer 3 bénévoles (IDs 6, 10 et 4) – dont 1 déjà utilisé dans la collecte 1, c'est possible
INSERT INTO collecte_benevole (collecte_id, benevole_id, confirme) VALUES
    (@collecte2_id, 6, 0),
    (@collecte2_id, 10, 0),
    (@collecte2_id, 4, 0);

UPDATE collecte SET nb_benevoles = 3 WHERE id = @collecte2_id;

-- ------------------------------------------------------------
-- 3. Vérification des insertions
-- ------------------------------------------------------------
SELECT '=== COLLECTES INSÉRÉES ===' AS '';
SELECT 
    c.id AS collecte_id,
    c.date_heure_collecte,
    c.adresse_collecte,
    c.statut AS collecte_statut,
    c.validee,
    c.nb_benevoles,
    u.nom AS commercant
FROM collecte c
JOIN commercant cm ON c.commercant_id = cm.id
JOIN utilisateur u ON cm.id = u.id
WHERE c.id IN (@collecte1_id, @collecte2_id)
ORDER BY c.id;

SELECT '=== PRODUITS PAR COLLECTE ===' AS '';
SELECT 
    p.collecte_id,
    p.id AS produit_id,
    p.nom,
    p.categorie,
    p.quantite,
    p.date_peremption,
    p.statut
FROM produit p
WHERE p.collecte_id IN (@collecte1_id, @collecte2_id)
ORDER BY p.collecte_id, p.id;

SELECT '=== BÉNÉVOLES ASSOCIÉS ===' AS '';
SELECT 
    cb.collecte_id,
    cb.benevole_id,
    u.nom,
    u.prenom,
    cb.confirme
FROM collecte_benevole cb
JOIN utilisateur u ON cb.benevole_id = u.id
WHERE cb.collecte_id IN (@collecte1_id, @collecte2_id)
ORDER BY cb.collecte_id, cb.benevole_id;