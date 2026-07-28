-- ============================================================
-- BASE DE DONNÉES NO MORE WASTE
-- ============================================================

CREATE DATABASE IF NOT EXISTS nomorewaste;
USE nomorewaste;

-- ============================================================
-- TABLE UTILISATEUR
-- ============================================================
CREATE TABLE utilisateur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    telephone VARCHAR(20),
    adresse TEXT,
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
    est_actif BOOLEAN DEFAULT TRUE,
    langue_preferee VARCHAR(5) DEFAULT 'fr',
    type_utilisateur VARCHAR(20) NOT NULL
);

-- ============================================================
-- TABLE COMMERCANT
-- ============================================================
CREATE TABLE commercant (
    id INT PRIMARY KEY,
    siret VARCHAR(14) UNIQUE NOT NULL,
    raison_sociale VARCHAR(255) NOT NULL,
    type_commerce VARCHAR(100),
    date_debut_adhesion DATE NOT NULL,
    date_fin_adhesion DATE NOT NULL,
    est_renouvele_automatiquement BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (id) REFERENCES utilisateur(id) ON DELETE CASCADE
);

-- ============================================================
-- TABLE BENEVOLE
-- ============================================================
CREATE TABLE benevole (
    id INT PRIMARY KEY,
    date_candidature DATE DEFAULT CURRENT_DATE,
    statut_candidature VARCHAR(20) DEFAULT 'En attente',
    FOREIGN KEY (id) REFERENCES utilisateur(id) ON DELETE CASCADE
);

-- ============================================================
-- TABLE ADHERENT
-- ============================================================
CREATE TABLE adherent (
    id INT PRIMARY KEY,
    date_debut_adhesion DATE NOT NULL,
    date_fin_adhesion DATE NOT NULL,
    FOREIGN KEY (id) REFERENCES utilisateur(id) ON DELETE CASCADE
);

-- ============================================================
-- TABLE COMPETENCE
-- ============================================================
CREATE TABLE competence (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT
);

-- ============================================================
-- TABLE BENEVOLE_COMPETENCE
-- ============================================================
CREATE TABLE benevole_competence (
    benevole_id INT,
    competence_id INT,
    PRIMARY KEY (benevole_id, competence_id),
    FOREIGN KEY (benevole_id) REFERENCES benevole(id) ON DELETE CASCADE,
    FOREIGN KEY (competence_id) REFERENCES competence(id) ON DELETE CASCADE
);

-- ============================================================
-- TABLE BENEVOLE_DISPONIBILITE
-- ============================================================
CREATE TABLE benevole_disponibilite (
    id INT AUTO_INCREMENT PRIMARY KEY,
    benevole_id INT,
    jour_semaine VARCHAR(10),
    heure_debut TIME,
    heure_fin TIME,
    FOREIGN KEY (benevole_id) REFERENCES benevole(id) ON DELETE CASCADE
);

-- ============================================================
-- TABLE COLLECTE
-- ============================================================
CREATE TABLE collecte (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date_heure_collecte DATETIME NOT NULL,
    adresse_collecte TEXT NOT NULL,
    statut VARCHAR(20) DEFAULT 'Planifiée',
    commentaire TEXT,
    commercant_id INT,
    FOREIGN KEY (commercant_id) REFERENCES commercant(id) ON DELETE SET NULL
);

-- ============================================================
-- TABLE PRODUIT
-- ============================================================
CREATE TABLE produit (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code_barre VARCHAR(50) NOT NULL,
    nom VARCHAR(255) NOT NULL,
    categorie VARCHAR(100),
    quantite INT NOT NULL DEFAULT 1,
    date_peremption DATE NOT NULL,
    date_entree_stock DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut VARCHAR(20) DEFAULT 'Stocké',
    collecte_id INT,
    FOREIGN KEY (collecte_id) REFERENCES collecte(id) ON DELETE SET NULL
);

-- ============================================================
-- TABLE LIEU_DISTRIBUTION
-- ============================================================
CREATE TABLE lieu_distribution (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    type VARCHAR(50) NOT NULL,
    adresse TEXT NOT NULL,
    personne_contact VARCHAR(255),
    telephone VARCHAR(20)
);

-- ============================================================
-- TABLE TOURNEE
-- ============================================================
CREATE TABLE tournee (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date_heure_depart DATETIME NOT NULL,
    date_heure_fin DATETIME,
    adresse_depart TEXT NOT NULL,
    statut VARCHAR(20) DEFAULT 'Prévue',
    benevole_id INT,
    lieu_distribution_id INT,
    FOREIGN KEY (benevole_id) REFERENCES benevole(id) ON DELETE SET NULL,
    FOREIGN KEY (lieu_distribution_id) REFERENCES lieu_distribution(id) ON DELETE SET NULL
);

-- ============================================================
-- TABLE TOURNEE_PRODUIT
-- ============================================================
CREATE TABLE tournee_produit (
    tournee_id INT,
    produit_id INT,
    PRIMARY KEY (tournee_id, produit_id),
    FOREIGN KEY (tournee_id) REFERENCES tournee(id) ON DELETE CASCADE,
    FOREIGN KEY (produit_id) REFERENCES produit(id) ON DELETE CASCADE
);

-- ============================================================
-- TABLE SERVICE
-- ============================================================
CREATE TABLE service (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    description TEXT,
    type VARCHAR(100)
);

-- ============================================================
-- TABLE SERVICE_PLANNING
-- ============================================================
CREATE TABLE service_planning (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date_heure_debut DATETIME NOT NULL,
    date_heure_fin DATETIME NOT NULL,
    capacite_max INT NOT NULL,
    statut VARCHAR(20) DEFAULT 'Ouvert',
    service_id INT,
    benevole_id INT,
    FOREIGN KEY (service_id) REFERENCES service(id) ON DELETE CASCADE,
    FOREIGN KEY (benevole_id) REFERENCES benevole(id) ON DELETE SET NULL
);

-- ============================================================
-- TABLE SERVICE_INSCRIPTION
-- ============================================================
CREATE TABLE service_inscription (
    adherent_id INT,
    service_planning_id INT,
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (adherent_id, service_planning_id),
    FOREIGN KEY (adherent_id) REFERENCES adherent(id) ON DELETE CASCADE,
    FOREIGN KEY (service_planning_id) REFERENCES service_planning(id) ON DELETE CASCADE
);

-- ============================================================
-- DONNÉES INITIALES
-- ============================================================

-- Compétences
INSERT INTO competence (nom, description) VALUES
('Chauffeur', 'Permis B, conduite de véhicule utilitaire'),
('Cuisinier', 'Préparation de repas et ateliers cuisine'),
('Plombier', 'Réparations et travaux de plomberie'),
('Electricien', 'Réparations et travaux d''électricité'),
('Bricoleur', 'Travaux de bricolage divers'),
('Jardinier', 'Entretien des espaces verts'),
('Informaticien', 'Aide à l''informatique et aux logiciels'),
('Animateur', 'Animation d''ateliers et d''événements');

-- Services
INSERT INTO service (nom, description, type) VALUES
('Conseils anti-gasp', 'Ateliers pour apprendre à réduire son gaspillage alimentaire', 'Atelier'),
('Cours de cuisine', 'Apprenez à cuisiner avec des produits de saison', 'Cours'),
('Partage de véhicules', 'Mise en relation pour le covoiturage', 'Service'),
('Échange de services', 'Entre particuliers (bricolage, électricité, plomberie)', 'Service'),
('Services de réparation', 'Réparation de petits appareils électroménagers', 'Service'),
('Gardiennage', 'Gardiennage de domiciles pendant les vacances', 'Service');

-- Lieux de distribution
INSERT INTO lieu_distribution (nom, type, adresse, personne_contact, telephone) VALUES
('Restos du Cœur Paris', 'Association', '12 Rue de la Solidarité, 75001 Paris', 'Jean Martin', '01 23 45 67 89'),
('Secours Populaire', 'Association', '45 Rue de l''Entraide, 75002 Paris', 'Marie Dupont', '01 98 76 54 32'),
('Centre d''Hébergement Saint-Jean', 'Association', '8 Rue de l''Espoir, 75003 Paris', 'Pierre Bernard', '01 45 67 89 12');

-- Admin par défaut (mot de passe: admin123)
INSERT INTO utilisateur (email, mot_de_passe, nom, prenom, type_utilisateur) VALUES
('admin@nomorewaste.org', '$2a$10$r0lKbYQ7YzUzV7YzUzV7YuYzUzV7YzUzV7YzUzV7YzUzV7YzUzV7YzU', 'Admin', 'NO MORE WASTE', 'responsable');