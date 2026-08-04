-- ============================================================
-- BASE DE DONNÉES NO MORE WASTE — script complet
-- Exécution : mysql -u root -p < scripts/init_db.sql
-- ============================================================

DROP DATABASE IF EXISTS nomorewaste;
CREATE DATABASE nomorewaste CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nomorewaste;

-- ============================================================
-- TABLES
-- ============================================================

CREATE TABLE agence (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    ville VARCHAR(100) NOT NULL,
    adresse TEXT NOT NULL,
    telephone VARCHAR(20),
    est_siege BOOLEAN DEFAULT FALSE
);

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

CREATE TABLE commercant (
    id INT PRIMARY KEY,
    siret VARCHAR(14) UNIQUE NOT NULL,
    raison_sociale VARCHAR(255) NOT NULL,
    type_commerce VARCHAR(100),
    date_debut_adhesion DATE NOT NULL,
    date_fin_adhesion DATE NOT NULL,
    est_renouvele_automatiquement BOOLEAN DEFAULT FALSE,
    agence_id INT,
    FOREIGN KEY (id) REFERENCES utilisateur(id) ON DELETE CASCADE,
    FOREIGN KEY (agence_id) REFERENCES agence(id) ON DELETE SET NULL
);

CREATE TABLE benevole (
    id INT PRIMARY KEY,
    date_candidature DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut_candidature VARCHAR(20) DEFAULT 'En attente',
    agence_id INT,
    FOREIGN KEY (id) REFERENCES utilisateur(id) ON DELETE CASCADE,
    FOREIGN KEY (agence_id) REFERENCES agence(id) ON DELETE SET NULL
);

CREATE TABLE adherent (
    id INT PRIMARY KEY,
    date_debut_adhesion DATE NOT NULL,
    date_fin_adhesion DATE NOT NULL,
    FOREIGN KEY (id) REFERENCES utilisateur(id) ON DELETE CASCADE
);

CREATE TABLE competence (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT
);

CREATE TABLE benevole_competence (
    benevole_id INT,
    competence_id INT,
    PRIMARY KEY (benevole_id, competence_id),
    FOREIGN KEY (benevole_id) REFERENCES benevole(id) ON DELETE CASCADE,
    FOREIGN KEY (competence_id) REFERENCES competence(id) ON DELETE CASCADE
);

CREATE TABLE benevole_disponibilite (
    id INT AUTO_INCREMENT PRIMARY KEY,
    benevole_id INT,
    jour_semaine VARCHAR(10),
    heure_debut TIME,
    heure_fin TIME,
    FOREIGN KEY (benevole_id) REFERENCES benevole(id) ON DELETE CASCADE
);

CREATE TABLE collecte (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date_heure_collecte DATETIME NOT NULL,
    adresse_collecte TEXT NOT NULL,
    statut VARCHAR(20) DEFAULT 'Planifiée',
    commentaire TEXT,
    commercant_id INT,
    agence_id INT,
    FOREIGN KEY (commercant_id) REFERENCES commercant(id) ON DELETE SET NULL,
    FOREIGN KEY (agence_id) REFERENCES agence(id) ON DELETE SET NULL
);

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

CREATE TABLE lieu_distribution (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    type VARCHAR(50) NOT NULL,
    adresse TEXT NOT NULL,
    personne_contact VARCHAR(255),
    telephone VARCHAR(20),
    agence_id INT,
    FOREIGN KEY (agence_id) REFERENCES agence(id) ON DELETE SET NULL
);

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

CREATE TABLE tournee_produit (
    tournee_id INT,
    produit_id INT,
    PRIMARY KEY (tournee_id, produit_id),
    FOREIGN KEY (tournee_id) REFERENCES tournee(id) ON DELETE CASCADE,
    FOREIGN KEY (produit_id) REFERENCES produit(id) ON DELETE CASCADE
);

CREATE TABLE service (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    description TEXT,
    type VARCHAR(100)
);

CREATE TABLE service_planning (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date_heure_debut DATETIME NOT NULL,
    date_heure_fin DATETIME NOT NULL,
    capacite_max INT NOT NULL,
    statut VARCHAR(20) DEFAULT 'Ouvert',
    service_id INT,
    benevole_id INT,
    agence_id INT,
    FOREIGN KEY (service_id) REFERENCES service(id) ON DELETE CASCADE,
    FOREIGN KEY (benevole_id) REFERENCES benevole(id) ON DELETE SET NULL,
    FOREIGN KEY (agence_id) REFERENCES agence(id) ON DELETE SET NULL
);

CREATE TABLE service_inscription (
    adherent_id INT,
    service_planning_id INT,
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (adherent_id, service_planning_id),
    FOREIGN KEY (adherent_id) REFERENCES adherent(id) ON DELETE CASCADE,
    FOREIGN KEY (service_planning_id) REFERENCES service_planning(id) ON DELETE CASCADE
);

CREATE TABLE rappel_renouvellement (
    id INT AUTO_INCREMENT PRIMARY KEY,
    commercant_id INT NOT NULL,
    date_rappel DATE NOT NULL,
    est_envoye BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (commercant_id) REFERENCES commercant(id) ON DELETE CASCADE
);

-- ============================================================
-- DONNÉES — Agences
-- ============================================================

INSERT INTO agence (id, nom, ville, adresse, telephone, est_siege) VALUES
(1, 'Siège Paris', 'Paris', '15 Rue du Recyclage, 75001 Paris', '0140000001', TRUE),
(2, 'Agence Nantes', 'Nantes', '8 Quai de la Solidarité, 44000 Nantes', '0240000002', FALSE),
(3, 'Agence Marseille', 'Marseille', '22 Boulevard du Partage, 13001 Marseille', '0440000003', FALSE),
(4, 'Agence Limoges', 'Limoges', '5 Place de l''Entraide, 87000 Limoges', '0550000004', FALSE);

-- ============================================================
-- DONNÉES — Référentiels
-- ============================================================

INSERT INTO competence (nom, description) VALUES
('Chauffeur', 'Permis B, conduite de véhicule utilitaire'),
('Cuisinier', 'Préparation de repas et ateliers cuisine'),
('Plombier', 'Réparations et travaux de plomberie'),
('Electricien', 'Réparations et travaux d''électricité'),
('Bricoleur', 'Travaux de bricolage divers'),
('Jardinier', 'Entretien des espaces verts'),
('Informaticien', 'Aide à l''informatique et aux logiciels'),
('Animateur', 'Animation d''ateliers et d''événements');

INSERT INTO service (nom, description, type) VALUES
('Conseils anti-gaspillage', 'Ateliers pour réduire le gaspillage alimentaire', 'Atelier'),
('Cours de cuisine', 'Cuisiner avec des produits de saison', 'Cours'),
('Partage de véhicules', 'Mise en relation pour le covoiturage', 'Service'),
('Échange de services', 'Bricolage, électricité, plomberie entre adhérents', 'Service'),
('Services de réparation', 'Réparation de petits appareils', 'Service'),
('Gardiennage', 'Gardiennage de domiciles', 'Service');

INSERT INTO lieu_distribution (nom, type, adresse, personne_contact, telephone, agence_id) VALUES
('Restos du Cœur Paris', 'Association', '12 Rue de la Solidarité, 75001 Paris', 'Jean Martin', '0123456789', 1),
('Secours Populaire Paris', 'Association', '45 Rue de l''Entraide, 75002 Paris', 'Marie Dupont', '0198765432', 1),
('Centre d''Hébergement Saint-Jean', 'Association', '8 Rue de l''Espoir, 75003 Paris', 'Pierre Bernard', '0145678912', 1),
('Banque Alimentaire Nantes', 'Association', '3 Rue du Don, 44000 Nantes', 'Sophie Leclerc', '0240123456', 2),
('Croix-Rouge Marseille', 'Association', '10 Avenue de l''Aide, 13001 Marseille', 'Luc Moreau', '0440987654', 3);

-- ============================================================
-- DONNÉES — Utilisateurs
-- ============================================================

-- Admin (mot de passe : admin123)
INSERT INTO utilisateur (id, email, mot_de_passe, nom, prenom, type_utilisateur, langue_preferee) VALUES
(1, 'admin@nomorewaste.org', 'admin123', 'Admin', 'NO MORE WASTE', 'responsable', 'fr');

-- Commerçants
INSERT INTO utilisateur (id, email, mot_de_passe, nom, prenom, telephone, adresse, type_utilisateur) VALUES
(2, 'boulangerie.dupont@mail.fr', 'commercant1', 'Dupont', 'Claire', '0611223344', '10 Rue de la Paix, 75002 Paris', 'commercant'),
(3, 'supermarche.martin@mail.fr', 'commercant2', 'Martin', 'Paul', '0699887766', '5 Avenue Victor Hugo, 75016 Paris', 'commercant'),
(9, 'epicerie.nantes@mail.fr', 'commercant3', 'Rousseau', 'Anne', '0612345678', '12 Rue du Commerce, 44000 Nantes', 'commercant');

INSERT INTO commercant (id, siret, raison_sociale, type_commerce, date_debut_adhesion, date_fin_adhesion, est_renouvele_automatiquement, agence_id) VALUES
(2, '12345678901234', 'Boulangerie Dupont', 'Alimentaire', '2025-01-01', '2026-12-31', TRUE, 1),
(3, '98765432109876', 'Supermarché Martin', 'Grande distribution', '2024-06-01', '2025-05-31', FALSE, 1),
(9, '45678901234567', 'Épicerie Bio Nantes', 'Alimentaire', '2025-03-01', '2026-02-28', TRUE, 2);

-- Bénévoles
INSERT INTO utilisateur (id, email, mot_de_passe, nom, prenom, telephone, type_utilisateur) VALUES
(4, 'jean.chauffeur@mail.fr', 'benevole1', 'Bernard', 'Jean', '0601020304', 'benevole'),
(5, 'marie.cuisine@mail.fr', 'benevole2', 'Leroy', 'Marie', '0709080706', 'benevole'),
(6, 'pierre.attente@mail.fr', 'benevole3', 'Petit', 'Pierre', '0612131415', 'benevole'),
(10, 'sarah.plombier@mail.fr', 'benevole4', 'Durand', 'Sarah', '0622334455', 'benevole');

INSERT INTO benevole (id, statut_candidature, agence_id) VALUES
(4, 'Validé', 1),
(5, 'Validé', 1),
(6, 'En attente', 1),
(10, 'Validé', 2);

INSERT INTO benevole_competence (benevole_id, competence_id) VALUES
(4, 1), (5, 2), (6, 5), (10, 3), (10, 4);

INSERT INTO benevole_disponibilite (benevole_id, jour_semaine, heure_debut, heure_fin) VALUES
(4, 'Lundi', '08:00:00', '12:00:00'),
(4, 'Mercredi', '14:00:00', '18:00:00'),
(5, 'Mardi', '09:00:00', '17:00:00'),
(10, 'Jeudi', '10:00:00', '16:00:00');

-- Adhérents
INSERT INTO utilisateur (id, email, mot_de_passe, nom, prenom, telephone, type_utilisateur) VALUES
(7, 'sophie.adherent@mail.fr', 'adherent1', 'Moreau', 'Sophie', '0611223344', 'adherent'),
(8, 'luc.adherent@mail.fr', 'adherent2', 'Girard', 'Luc', '0699887766', 'adherent'),
(11, 'emma.adherent@mail.fr', 'adherent3', 'Blanc', 'Emma', '0677889900', 'adherent');

INSERT INTO adherent (id, date_debut_adhesion, date_fin_adhesion) VALUES
(7, '2025-01-15', '2026-01-14'),
(8, '2025-03-01', '2026-02-28'),
(11, '2025-06-01', '2026-05-31');

-- ============================================================
-- DONNÉES — Collectes & Produits
-- ============================================================

INSERT INTO collecte (id, date_heure_collecte, adresse_collecte, statut, commentaire, commercant_id, agence_id) VALUES
(1, '2026-03-10 07:30:00', '10 Rue de la Paix, 75002 Paris', 'Terminée', 'Invendus du matin', 2, 1),
(2, '2026-03-15 18:00:00', '5 Avenue Victor Hugo, 75016 Paris', 'Planifiée', 'Collecte du soir', 3, 1),
(3, '2026-03-08 12:00:00', '10 Rue de la Paix, 75002 Paris', 'Terminée', NULL, 2, 1),
(4, '2026-03-12 09:00:00', '12 Rue du Commerce, 44000 Nantes', 'Planifiée', 'Collecte hebdomadaire', 9, 2);

INSERT INTO produit (code_barre, nom, categorie, quantite, date_peremption, statut, collecte_id) VALUES
('3760123456789', 'Pain de campagne', 'Boulangerie', 20, '2026-03-12', 'Stocké', 1),
('3760123456790', 'Croissants assortis', 'Boulangerie', 30, '2026-03-11', 'Stocké', 1),
('3017620422003', 'Confiture fraise', 'Épicerie', 15, '2026-08-01', 'Stocké', 3),
('3560070460096', 'Yaourts nature x12', 'Produits laitiers', 8, '2026-03-20', 'En tournée', 1),
('3228857000902', 'Salade mélangée', 'Frais', 12, '2026-03-14', 'Stocké', 3),
('3760123456791', 'Baguettes tradition', 'Boulangerie', 25, '2026-03-13', 'Stocké', 1),
('3017620422004', 'Pâtes bio 500g', 'Épicerie', 40, '2027-01-01', 'Stocké', 3),
('3560070460097', 'Fromage blanc x6', 'Produits laitiers', 10, '2026-03-18', 'Stocké', 1);

-- ============================================================
-- DONNÉES — Tournées
-- ============================================================

INSERT INTO tournee (id, date_heure_depart, adresse_depart, statut, benevole_id, lieu_distribution_id) VALUES
(1, '2026-03-11 09:00:00', 'Siège NO MORE WASTE, 75001 Paris', 'Prévue', 4, 1),
(2, '2026-03-09 14:00:00', 'Siège NO MORE WASTE, 75001 Paris', 'Terminée', 4, 2);

UPDATE tournee SET date_heure_fin = '2026-03-09 17:30:00' WHERE id = 2;

INSERT INTO tournee_produit (tournee_id, produit_id) VALUES
(1, 4),
(2, 2);

UPDATE produit SET statut = 'Distribué' WHERE id = 2;

-- ============================================================
-- DONNÉES — Services & Plannings
-- ============================================================

INSERT INTO service_planning (date_heure_debut, date_heure_fin, capacite_max, statut, service_id, benevole_id, agence_id) VALUES
('2026-04-05 10:00:00', '2026-04-05 12:00:00', 15, 'Ouvert', 1, 5, 1),
('2026-04-12 14:00:00', '2026-04-12 17:00:00', 10, 'Ouvert', 2, 5, 1),
('2026-04-20 09:00:00', '2026-04-20 11:00:00', 20, 'Ouvert', 1, 5, 2),
('2026-05-03 15:00:00', '2026-05-03 18:00:00', 8, 'Ouvert', 5, 10, 2);

INSERT INTO service_inscription (adherent_id, service_planning_id) VALUES
(7, 1),
(8, 1),
(11, 3);

-- ============================================================
-- DONNÉES — Rappels renouvellement
-- ============================================================

INSERT INTO rappel_renouvellement (commercant_id, date_rappel, est_envoye) VALUES
(3, '2025-04-01', FALSE),
(3, '2025-05-01', FALSE);
