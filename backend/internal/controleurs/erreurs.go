package controleurs

import (
	"encoding/json"
	"net/http"
)

// ecrireErreurJSON renvoie une erreur au format JSON ({"error": msg}) au lieu du texte brut de
// http.Error : ClientApi.php (côté PHP) fait systématiquement du json_decode() sur les réponses,
// donc un corps texte brut devient `null` côté PHP et le message précis (email déjà utilisé, mot
// de passe trop faible...) n'atteint jamais l'utilisateur si on ne passe pas par cette fonction.
func ecrireErreurJSON(w http.ResponseWriter, msg string, code int) {
	w.Header().Set("Content-Type", "application/json")
	w.WriteHeader(code)
	json.NewEncoder(w).Encode(map[string]string{"error": msg})
}
