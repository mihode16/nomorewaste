package modeles

import (
	"time"
)

// ============================================================
// UTILISATEUR
// ============================================================
type Utilisateur struct {
    ID              int       `json:"id"`
    Email           string    `json:"email"`
    MotDePasse      string    `json:"mot_de_passe,omitempty"`
    Nom             string    `json:"nom"`
    Prenom          string    `json:"prenom"`
    Telephone       string    `json:"telephone"`
    Adresse         string    `json:"adresse"`
    DateInscription time.Time `json:"date_inscription"`
    EstActif        bool      `json:"est_actif"`
    LanguePreferee  string    `json:"langue_preferee"`
    TypeUtilisateur string    `json:"type_utilisateur"`
}

// ============================================================
// COMMERCANT
// ============================================================
type Commercant struct {
    Utilisateur
    Siret                     string    `json:"siret"`
    RaisonSociale             string    `json:"raison_sociale"`
    TypeCommerce              string    `json:"type_commerce"`
    DateDebutAdhesion         time.Time `json:"date_debut_adhesion"`
    DateFinAdhesion           time.Time `json:"date_fin_adhesion"`
    EstRenouveleAutomatiquement bool    `json:"est_renouvele_automatiquement"`
}

type CommercantCreation struct {
    Email           string `json:"email"`
    MotDePasse      string `json:"mot_de_passe"`
    Nom             string `json:"nom"`
    Prenom          string `json:"prenom"`
    Telephone       string `json:"telephone"`
    Adresse         string `json:"adresse"`
    Siret           string `json:"siret"`
    RaisonSociale   string `json:"raison_sociale"`
    TypeCommerce    string `json:"type_commerce"`
    DateDebutAdhesion string `json:"date_debut_adhesion"`
    DateFinAdhesion   string `json:"date_fin_adhesion"`
    EstRenouveleAutomatiquement bool `json:"est_renouvele_automatiquement"`
}

// ============================================================
// BENEVOLE
// ============================================================
type Benevole struct {
    Utilisateur
    DateCandidature    time.Time `json:"date_candidature"`
    StatutCandidature  string    `json:"statut_candidature"`
    Competences        []Competence `json:"competences,omitempty"`
    Disponibilites     []Disponibilite `json:"disponibilites,omitempty"`
}

type BenevoleCreation struct {
    Email          string `json:"email"`
    MotDePasse     string `json:"mot_de_passe"`
    Nom            string `json:"nom"`
    Prenom         string `json:"prenom"`
    Telephone      string `json:"telephone"`
    Adresse        string `json:"adresse"`
    Competences    []int  `json:"competences"`
}

type BenevoleCandidature struct {
    ID     int `json:"id"`
    Statut string `json:"statut"`
}

// ============================================================
// COMPETENCE
// ============================================================
type Competence struct {
    ID          int    `json:"id"`
    Nom         string `json:"nom"`
    Description string `json:"description"`
}

// ============================================================
// DISPONIBILITE
// ============================================================
type Disponibilite struct {
    ID          int       `json:"id"`
    BenevoleID  int       `json:"benevole_id"`
    JourSemaine string    `json:"jour_semaine"`
    HeureDebut  string    `json:"heure_debut"`
    HeureFin    string    `json:"heure_fin"`
}

// ============================================================
// COLLECTE
// ============================================================
type Collecte struct {
    ID              int       `json:"id"`
    DateHeureCollecte time.Time `json:"date_heure_collecte"`
    AdresseCollecte string    `json:"adresse_collecte"`
    Statut          string    `json:"statut"`
    Commentaire     string    `json:"commentaire"`
    CommercantID    int       `json:"commercant_id"`
    Commercant      *Commercant `json:"commercant,omitempty"`
}

type CollecteCreation struct {
    DateHeureCollecte string `json:"date_heure_collecte"`
    AdresseCollecte   string `json:"adresse_collecte"`
    CommercantID      int    `json:"commercant_id"`
    Commentaire       string `json:"commentaire"`
}

// ============================================================
// PRODUIT
// ============================================================
type Produit struct {
    ID             int       `json:"id"`
    CodeBarre      string    `json:"code_barre"`
    Nom            string    `json:"nom"`
    Categorie      string    `json:"categorie"`
    Quantite       int       `json:"quantite"`
    DatePeremption time.Time `json:"date_peremption"`
    DateEntreeStock time.Time `json:"date_entree_stock"`
    Statut         string    `json:"statut"`
    CollecteID     int       `json:"collecte_id"`
    Collecte       *Collecte `json:"collecte,omitempty"`
}

type ProduitCreation struct {
    CodeBarre      string `json:"code_barre"`
    Nom            string `json:"nom"`
    Categorie      string `json:"categorie"`
    Quantite       int    `json:"quantite"`
    DatePeremption string `json:"date_peremption"`
    CollecteID     int    `json:"collecte_id"`
}

// ============================================================
// TOURNEE
// ============================================================
type Tournee struct {
    ID                int       `json:"id"`
    DateHeureDepart   time.Time `json:"date_heure_depart"`
    DateHeureFin      *time.Time `json:"date_heure_fin,omitempty"`
    AdresseDepart     string    `json:"adresse_depart"`
    Statut            string    `json:"statut"`
    BenevoleID        int       `json:"benevole_id"`
    LieuDistributionID int      `json:"lieu_distribution_id"`
    Benevole          *Benevole `json:"benevole,omitempty"`
    LieuDistribution  *LieuDistribution `json:"lieu_distribution,omitempty"`
    Produits          []Produit `json:"produits,omitempty"`
}

type TourneeCreation struct {
    DateHeureDepart   string `json:"date_heure_depart"`
    AdresseDepart     string `json:"adresse_depart"`
    BenevoleID        int    `json:"benevole_id"`
    LieuDistributionID int   `json:"lieu_distribution_id"`
    ProduitsIDs       []int  `json:"produits_ids"`
}

// ============================================================
// LIEU_DISTRIBUTION
// ============================================================
type LieuDistribution struct {
    ID             int    `json:"id"`
    Nom            string `json:"nom"`
    Type           string `json:"type"`
    Adresse        string `json:"adresse"`
    PersonneContact string `json:"personne_contact"`
    Telephone      string `json:"telephone"`
}

// ============================================================
// SERVICE
// ============================================================
type Service struct {
    ID          int    `json:"id"`
    Nom         string `json:"nom"`
    Description string `json:"description"`
    Type        string `json:"type"`
}

// ============================================================
// SERVICE_PLANNING
// ============================================================
type ServicePlanning struct {
    ID             int       `json:"id"`
    DateHeureDebut time.Time `json:"date_heure_debut"`
    DateHeureFin   time.Time `json:"date_heure_fin"`
    CapaciteMax    int       `json:"capacite_max"`
    Statut         string    `json:"statut"`
    ServiceID      int       `json:"service_id"`
    BenevoleID     int       `json:"benevole_id"`
    Service        *Service  `json:"service,omitempty"`
    Benevole       *Benevole `json:"benevole,omitempty"`
}

type ServicePlanningCreation struct {
    DateHeureDebut string `json:"date_heure_debut"`
    DateHeureFin   string `json:"date_heure_fin"`
    CapaciteMax    int    `json:"capacite_max"`
    ServiceID      int    `json:"service_id"`
    BenevoleID     int    `json:"benevole_id"`
}