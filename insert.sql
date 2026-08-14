CREATE TABLE IF NOT EXISTS tournee_benevole (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tournee_id INT NOT NULL,
    benevole_id INT NOT NULL,
    confirme TINYINT(1) DEFAULT 0,
    date_confirmation DATETIME NULL,
    FOREIGN KEY (tournee_id) REFERENCES tournee(id) ON DELETE CASCADE,
    FOREIGN KEY (benevole_id) REFERENCES benevole(id) ON DELETE CASCADE,
    UNIQUE KEY (tournee_id, benevole_id)
);

ALTER TABLE tournee
    ADD COLUMN IF NOT EXISTS chauffeur_confirme TINYINT(1) DEFAULT 0,
    ADD COLUMN IF NOT EXISTS date_confirmation_chauffeur DATETIME NULL;

ALTER TABLE adherent
    ADD COLUMN IF NOT EXISTS statut_adhesion VARCHAR(20) DEFAULT 'en_attente',
    ADD COLUMN IF NOT EXISTS demande_renouvellement TINYINT(1) DEFAULT 0,
    ADD COLUMN IF NOT EXISTS est_renouvele_automatiquement TINYINT(1) DEFAULT 0;

CREATE TABLE IF NOT EXISTS parametre (
    cle VARCHAR(50) PRIMARY KEY,
    valeur VARCHAR(255) NOT NULL
);
INSERT INTO parametre (cle, valeur) VALUES ('prix_adhesion_mensuel', '7.99')
    ON DUPLICATE KEY UPDATE valeur = valeur;

-- Discussions génériques entre utilisateurs (commerçant/adhérent <-> admin, ou adhérent <-> adhérent).
-- type='admin' : initiateur_id = le commerçant/adhérent, destinataire_id NULL, visible par tous les admins ;
-- seul un admin peut clôturer ce type de conversation.
-- type='pair' : messagerie privée entre deux adhérents (initiateur_id / destinataire_id).
CREATE TABLE IF NOT EXISTS conversation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('admin','pair') NOT NULL DEFAULT 'admin',
    initiateur_id INT NOT NULL,
    destinataire_id INT NULL,
    collecte_id INT NULL,
    sujet VARCHAR(255) NOT NULL,
    cloturee TINYINT(1) DEFAULT 0,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_cloture DATETIME NULL,
    FOREIGN KEY (initiateur_id) REFERENCES utilisateur(id) ON DELETE CASCADE,
    FOREIGN KEY (destinataire_id) REFERENCES utilisateur(id) ON DELETE CASCADE,
    FOREIGN KEY (collecte_id) REFERENCES collecte(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS message (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT NOT NULL,
    expediteur_id INT NOT NULL,
    contenu TEXT NOT NULL,
    date_envoi DATETIME DEFAULT CURRENT_TIMESTAMP,
    lu TINYINT(1) DEFAULT 0,
    FOREIGN KEY (conversation_id) REFERENCES conversation(id) ON DELETE CASCADE,
    FOREIGN KEY (expediteur_id) REFERENCES utilisateur(id) ON DELETE CASCADE
);

-- Plannings Excel générés par un admin pour un bénévole, téléchargeables depuis son espace.
CREATE TABLE IF NOT EXISTS planning_benevole (
    id INT AUTO_INCREMENT PRIMARY KEY,
    benevole_id INT NOT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    date_generation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (benevole_id) REFERENCES benevole(id) ON DELETE CASCADE
);

-- Les disponibilités sont désormais rattachées à une date précise (semaine courante ou
-- suivante), et non plus à un jour de la semaine récurrent indéfiniment.
ALTER TABLE benevole_disponibilite
    ADD COLUMN IF NOT EXISTS date_dispo DATE NULL AFTER jour_semaine;

-- Compétence(s) requise(s) pour un service, éventuellement créée(s) à la volée depuis le
-- formulaire de création d'un service.
CREATE TABLE IF NOT EXISTS service_competence (
    service_id INT NOT NULL,
    competence_id INT NOT NULL,
    PRIMARY KEY (service_id, competence_id),
    FOREIGN KEY (service_id) REFERENCES service(id) ON DELETE CASCADE,
    FOREIGN KEY (competence_id) REFERENCES competence(id) ON DELETE CASCADE
);