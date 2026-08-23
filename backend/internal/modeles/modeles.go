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
    StatutAdhesion            string    `json:"statut_adhesion"`
    DemandeRenouvellement     bool      `json:"demande_renouvellement"` // AJOUT
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
    // StatutAdhesion n'est pas requis car il sera défini par défaut en 'en_attente'
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

type BenevoleMiseAJour struct {
    Nom       string `json:"nom"`
    Prenom    string `json:"prenom"`
    Telephone string `json:"telephone"`
    Adresse   string `json:"adresse"`
}

// ============================================================
// PLANNING_BENEVOLE (export Excel du planning d'un bénévole)
// ============================================================
type PlanningBenevole struct {
    ID             int       `json:"id"`
    BenevoleID     int       `json:"benevole_id"`
    DateDebut      time.Time `json:"date_debut"`
    DateFin        time.Time `json:"date_fin"`
    DateGeneration time.Time `json:"date_generation"`
}

type PlanningBenevoleCreation struct {
    BenevoleID int    `json:"benevole_id"`
    DateDebut  string `json:"date_debut"`
    DateFin    string `json:"date_fin"`
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
    ID             int    `json:"id"`
    BenevoleID     int    `json:"benevole_id"`
    JourSemaine    string `json:"jour_semaine"`
    Date           string `json:"date"` // date précise (YYYY-MM-DD) de la semaine courante ou suivante
    HeureDebut     string `json:"heure_debut"`
    HeureFin       string `json:"heure_fin"`
    BenevoleNom    string `json:"benevole_nom,omitempty"`
    BenevolePrenom string `json:"benevole_prenom,omitempty"`
}

// ============================================================
// COLLECTE
// ============================================================
type Collecte struct {
    ID                 int        `json:"id"`
    DateHeureCollecte  time.Time  `json:"date_heure_collecte"`
    AdresseCollecte    string     `json:"adresse_collecte"`
    Statut             string     `json:"statut"`          // Peut être "" (en attente), "Planifiée", "Terminée"
    Commentaire        string     `json:"commentaire"`
    CommercantID       int        `json:"commercant_id"`
    Commercant         *Commercant `json:"commercant,omitempty"`
    Validee            bool       `json:"validee"`
    NbBenevoles        int        `json:"nb_benevoles"`    // Nombre de bénévoles requis
    Benevoles          []CollecteBenevole `json:"benevoles,omitempty"`
}

type CollecteCreation struct {
    DateHeureCollecte string `json:"date_heure_collecte"`
    AdresseCollecte   string `json:"adresse_collecte"`
    CommercantID      int    `json:"commercant_id"`
    Commentaire       string `json:"commentaire"`
    // Validee non demandé, sera mis à 0 par défaut
}

type CollecteBenevole struct {
    ID              int        `json:"id"`
    CollecteID      int        `json:"collecte_id"`
    BenevoleID      int        `json:"benevole_id"`
    Confirme        bool       `json:"confirme"`
    DateConfirmation *time.Time `json:"date_confirmation,omitempty"`
    BenevoleNom     string     `json:"benevole_nom,omitempty"`
    BenevolePrenom  string     `json:"benevole_prenom,omitempty"`
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
    ID                        int        `json:"id"`
    DateHeureDepart           time.Time  `json:"date_heure_depart"`
    DateHeureFin              *time.Time `json:"date_heure_fin,omitempty"`
    AdresseDepart             string     `json:"adresse_depart"`
    Statut                    string     `json:"statut"`
    BenevoleID                int        `json:"benevole_id"`
    LieuDistributionID        int        `json:"lieu_distribution_id"`
    ChauffeurConfirme         bool       `json:"chauffeur_confirme"`
    DateConfirmationChauffeur *time.Time `json:"date_confirmation_chauffeur,omitempty"`
    Benevole                  *Benevole  `json:"benevole,omitempty"`
    LieuDistribution          *LieuDistribution `json:"lieu_distribution,omitempty"`
    Produits                  []Produit  `json:"produits,omitempty"`
    Benevoles                 []Benevole `json:"benevoles,omitempty"` // bénévoles supplémentaires
    BenevolesConfirmation     []TourneeBenevole `json:"benevoles_confirmation,omitempty"` // statut de confirmation des bénévoles supplémentaires
}

// TourneeBenevole représente la confirmation de fin de tournée par un bénévole supplémentaire
type TourneeBenevole struct {
    ID                int        `json:"id"`
    TourneeID         int        `json:"tournee_id"`
    BenevoleID        int        `json:"benevole_id"`
    Confirme          bool       `json:"confirme"`
    DateConfirmation  *time.Time `json:"date_confirmation,omitempty"`
    BenevoleNom       string     `json:"benevole_nom,omitempty"`
    BenevolePrenom    string     `json:"benevole_prenom,omitempty"`
}

type TourneeCreation struct {
    DateHeureDepart    string `json:"date_heure_depart"`
    AdresseDepart      string `json:"adresse_depart"`
    BenevoleID         int    `json:"benevole_id"`
    LieuDistributionID int    `json:"lieu_distribution_id"`
    ProduitsIDs        []int  `json:"produits_ids"`
    BenevolesIDs       []int  `json:"benevoles_ids"` // bénévoles supplémentaires
}

type TourneeUpdate struct {
    ID                 int    `json:"id"`
    DateHeureDepart    string `json:"date_heure_depart"`
    AdresseDepart      string `json:"adresse_depart"`
    BenevoleID         int    `json:"benevole_id"`
    LieuDistributionID int    `json:"lieu_distribution_id"`
    ProduitsIDs        []int  `json:"produits_ids"`
    BenevolesIDs       []int  `json:"benevoles_ids"`
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
    ID          int          `json:"id"`
    Nom         string       `json:"nom"`
    Description string       `json:"description"`
    Type        string       `json:"type"`
    Competences []Competence `json:"competences,omitempty"`
}

// ServiceCreation : NouvelleCompetence facultatif — s'il est renseigné, une compétence est
// créée et associée au service en même temps que sa création.
type ServiceCreation struct {
    Nom                    string `json:"nom"`
    Description            string `json:"description"`
    Type                   string `json:"type"`
    NouvelleCompetence     string `json:"nouvelle_competence"`
    DescriptionCompetence  string `json:"description_competence"`
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
    NbInscrits     int       `json:"nb_inscrits"`
}

type ServicePlanningCreation struct {
    DateHeureDebut string `json:"date_heure_debut"`
    DateHeureFin   string `json:"date_heure_fin"`
    CapaciteMax    int    `json:"capacite_max"`
    ServiceID      int    `json:"service_id"`
    BenevoleID     int    `json:"benevole_id"`
}

type ServicePlanningUpdate struct {
    ID             int    `json:"id"`
    DateHeureDebut string `json:"date_heure_debut"`
    DateHeureFin   string `json:"date_heure_fin"`
    CapaciteMax    int    `json:"capacite_max"`
    ServiceID      int    `json:"service_id"`
    BenevoleID     int    `json:"benevole_id"`
    Statut         string `json:"statut"`
}

// ============================================================
// ADHERENT
// ============================================================
type Adherent struct {
    Utilisateur
    DateDebutAdhesion           time.Time `json:"date_debut_adhesion"`
    DateFinAdhesion             time.Time `json:"date_fin_adhesion"`
    StatutAdhesion              string    `json:"statut_adhesion"`
    DemandeRenouvellement       bool      `json:"demande_renouvellement"`
    EstRenouveleAutomatiquement bool      `json:"est_renouvele_automatiquement"`
}

type AdherentCreation struct {
    Email                       string `json:"email"`
    MotDePasse                  string `json:"mot_de_passe"`
    Nom                         string `json:"nom"`
    Prenom                      string `json:"prenom"`
    Telephone                   string `json:"telephone"`
    Adresse                     string `json:"adresse"`
    DateDebutAdhesion           string `json:"date_debut_adhesion"`
    DateFinAdhesion             string `json:"date_fin_adhesion"`
    EstRenouveleAutomatiquement bool   `json:"est_renouvele_automatiquement"`
}

type LoginRequest struct {
    Email      string `json:"email"`
    MotDePasse string `json:"mot_de_passe"`
}

// ============================================================
// CONVERSATION / MESSAGE
// ============================================================

// ParticipantConversation résume l'auteur d'une conversation ou d'un message (nom/prénom +
// éléments d'affichage propres à son profil, ex. raison sociale pour un commerçant).
type ParticipantConversation struct {
    ID              int    `json:"id"`
    Nom             string `json:"nom"`
    Prenom          string `json:"prenom"`
    TypeUtilisateur string `json:"type_utilisateur"`
    RaisonSociale   string `json:"raison_sociale,omitempty"`
}

type Conversation struct {
    ID             int                       `json:"id"`
    Type           string                    `json:"type"` // "admin" ou "pair"
    InitiateurID   int                       `json:"initiateur_id"`
    DestinataireID *int                      `json:"destinataire_id,omitempty"`
    CollecteID     *int                      `json:"collecte_id,omitempty"`
    Sujet          string                    `json:"sujet"`
    Cloturee       bool                      `json:"cloturee"`
    DateCreation   time.Time                 `json:"date_creation"`
    DateCloture    *time.Time                `json:"date_cloture,omitempty"`
    Initiateur     *ParticipantConversation  `json:"initiateur,omitempty"`
    Destinataire   *ParticipantConversation  `json:"destinataire,omitempty"`
    Messages       []Message                 `json:"messages,omitempty"`
    NbNonLus       int                       `json:"nb_non_lus"`
}

// ConversationCreation : Type="admin" ouvre une discussion avec l'association (DestinataireID
// ignoré) ; Type="pair" ouvre une messagerie privée entre deux adhérents (DestinataireID requis).
type ConversationCreation struct {
    Type           string `json:"type"`
    InitiateurID   int    `json:"initiateur_id"`
    DestinataireID int    `json:"destinataire_id"`
    CollecteID     int    `json:"collecte_id"`
    Sujet          string `json:"sujet"`
    Contenu        string `json:"contenu"`
}

type Message struct {
    ID             int                       `json:"id"`
    ConversationID int                       `json:"conversation_id"`
    ExpediteurID   int                       `json:"expediteur_id"`
    Expediteur     *ParticipantConversation  `json:"expediteur,omitempty"`
    Contenu        string                    `json:"contenu"`
    DateEnvoi      time.Time                 `json:"date_envoi"`
    Lu             bool                      `json:"lu"`
}

type MessageCreation struct {
    ExpediteurID int    `json:"expediteur_id"`
    Contenu      string `json:"contenu"`
}