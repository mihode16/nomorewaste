SET FOREIGN_KEY_CHECKS = 0;

-- ==================== UTILISATEUR ====================
DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE `utilisateur` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `adresse` text,
  `date_inscription` datetime DEFAULT CURRENT_TIMESTAMP,
  `est_actif` tinyint(1) DEFAULT '1',
  `langue_preferee` varchar(5) DEFAULT 'fr',
  `type_utilisateur` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `utilisateur` VALUES
(1,'admin@nomorewaste.org','$2a$10$eFF3X.MLxZFaZHJhZk96CeWBYZhCFyVodcGWHEowJh/xYmLFS9JDm','Admin','NO MORE WASTE',NULL,NULL,'2026-08-04 23:13:10',1,'fr','responsable'),
(2,'boulangerie.dupont@mail.fr','$2a$10$wFTk123nt57wHrjnHZngFePfOJ5IEfqlExD4iHHXCVGZIIpmFUvo.','Dupont','Claire','0611223344','10 Rue de la Paix, 75002 Paris','2026-08-04 23:13:10',1,'fr','commercant'),
(3,'supermarche.martin@mail.fr','$2a$10$5LdMPoN1XLhdPisUBM9s0u6Dtw3N1.mkFz3wyhFzY9ERxmBPQoTWu','Martin','Paul','0699887766','5 Avenue Victor Hugo, 75016 Paris','2026-08-04 23:13:10',1,'fr','commercant'),
(4,'jean.chauffeur@mail.fr','$2a$10$OoQkttQHoQ65ZzshQwgGde17v4M626s67EW7kUYyutS.Wiwsqsby6','Bernard','Jean','0601020304',NULL,'2026-08-04 23:13:11',1,'fr','benevole'),
(5,'marie.cuisine@mail.fr','$2a$10$bjEEGxaXqGQOzXv0xRvzde7b7HcKSvsV.nwjomyulIL2XFytHTtfS','Leroy','Marie','0709080706',NULL,'2026-08-04 23:13:11',1,'fr','benevole'),
(6,'pierre.attente@mail.fr','$2a$10$AjVZZNvXzy8W5ISgGmO4aeZs5sn3a6O7LvoThMW6ZcWNWPzKVdwea','Petit','Pierre','0612131415',NULL,'2026-08-04 23:13:11',1,'fr','benevole'),
(7,'sophie.adherent@mail.fr','$2a$10$xTrSLTMJ5HdCZixPjtAaUuNoVOTbex4Dsa7thcRAEn9w6OaFNul0W','Moreau','Sophie','0611223344','172 Rue de Paris, 75001 Paris','2026-08-04 23:13:12',1,'fr','adherent'),
(8,'luc.adherent@mail.fr','$2a$10$/lNQsie1OGSCz3ewB.F.ueyPCxEpYLD9okWpox5UREKlVMm.5AXEa','Girard','Luc','0699887766',NULL,'2026-08-04 23:13:12',1,'fr','adherent'),
(9,'epicerie.nantes@mail.fr','$2a$10$lh9ZJlbe/oJMmW0ZzUGxzuaMf/TN/kwEB14ihSyYYIWJEur.OxvV.','Rousseau','Anne','0612345678','12 Rue du Commerce, 44000 Nantes','2026-08-04 23:13:10',1,'fr','commercant'),
(10,'sarah.plombier@mail.fr','$2a$10$yjDFwvmyMjtR6Q7X/yScfevPh.x5qm/03GtRMG3LLKMepgdiFPxk6','Durand','Sarah','0622334455',NULL,'2026-08-04 23:13:11',1,'fr','benevole'),
(11,'emma.adherent@mail.fr','$2a$10$KZK8FpzOD2MxuKlEW263p.M7hY33I4VuD02Ggk0OjKP6APfnxg3kO','Blanc','Emma','0677889900',NULL,'2026-08-04 23:13:12',1,'fr','adherent'),
(12,'gracesse.hounkanrin@carene.fr','$2a$10$w24FwRDXP/KLcYZ95k3KBeSkIbwzEfJXErdyDPifnmcXopiQBxiku','Hounkanrin','Gracesse','0618278887','172 Rue de Paris, 75001 Paris','2026-08-04 23:53:33',1,'fr','benevole');

-- ==================== COMMERÇANT ====================
DROP TABLE IF EXISTS `commercant`;
CREATE TABLE `commercant` (
  `id` int(11) NOT NULL,
  `siret` varchar(14) NOT NULL,
  `raison_sociale` varchar(255) NOT NULL,
  `type_commerce` varchar(100) DEFAULT NULL,
  `date_debut_adhesion` date NOT NULL,
  `date_fin_adhesion` date NOT NULL,
  `est_renouvele_automatiquement` tinyint(1) DEFAULT '0',
  `statut_adhesion` varchar(20) DEFAULT 'en_attente',
  `demande_renouvellement` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `siret` (`siret`),
  CONSTRAINT `commercant_ibfk_1` FOREIGN KEY (`id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `commercant` VALUES
(2,'12345678901234','Boulangerie Dupont','Alimentaire','2024-12-28','2028-08-05',1,'valide',0),
(3,'98765432109876','Supermarché Martin','Alimentaire','2024-05-29','2028-08-13',0,'valide',0),
(9,'45678901234567','Épicerie Bio Nantes','Alimentaire','2025-02-28','2026-02-27',0,'valide',0);

-- ==================== ADHÉRENT ====================
DROP TABLE IF EXISTS `adherent`;
CREATE TABLE `adherent` (
  `id` int(11) NOT NULL,
  `date_debut_adhesion` date NOT NULL,
  `date_fin_adhesion` date NOT NULL,
  `statut_adhesion` varchar(20) DEFAULT 'en_attente',
  `demande_renouvellement` tinyint(1) DEFAULT '0',
  `est_renouvele_automatiquement` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  CONSTRAINT `adherent_ibfk_1` FOREIGN KEY (`id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `adherent` VALUES
(7,'2025-01-15','2027-02-13','valide',0,0),
(8,'2025-03-01','2026-02-28','valide',0,0),
(11,'2025-05-31','2026-09-12','valide',0,0);

-- ==================== BÉNÉVOLE ====================
DROP TABLE IF EXISTS `benevole`;
CREATE TABLE `benevole` (
  `id` int(11) NOT NULL,
  `date_candidature` datetime DEFAULT CURRENT_TIMESTAMP,
  `statut_candidature` varchar(20) DEFAULT 'En attente',
  PRIMARY KEY (`id`),
  CONSTRAINT `benevole_ibfk_1` FOREIGN KEY (`id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `benevole` VALUES
(4,'2026-08-04 23:13:11','Validé'),
(5,'2026-08-04 23:13:11','Refusé'),
(6,'2026-08-04 23:13:11','Validé'),
(10,'2026-08-04 23:13:11','Validé'),
(12,'2026-08-04 23:53:33','Validé');

-- ==================== COMPÉTENCE ====================
DROP TABLE IF EXISTS `competence`;
CREATE TABLE `competence` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `competence` VALUES
(1,'Chauffeur','Permis B, conduite de véhicule utilitaire'),
(2,'Cuisinier','Préparation de repas et ateliers cuisine'),
(3,'Plombier','Réparations et travaux de plomberie'),
(4,'Electricien','Réparations et travaux d\'électricité'),
(5,'Bricoleur','Travaux de bricolage divers'),
(6,'Jardinier','Entretien des espaces verts'),
(7,'Informaticien','Aide à l\'informatique et aux logiciels'),
(8,'Animateur','Animation d\'ateliers et d\'événements');

-- ==================== BÉNÉVOLE <-> COMPÉTENCE ====================
DROP TABLE IF EXISTS `benevole_competence`;
CREATE TABLE `benevole_competence` (
  `benevole_id` int(11) NOT NULL,
  `competence_id` int(11) NOT NULL,
  PRIMARY KEY (`benevole_id`,`competence_id`),
  KEY `competence_id` (`competence_id`),
  CONSTRAINT `benevole_competence_ibfk_1` FOREIGN KEY (`benevole_id`) REFERENCES `benevole` (`id`) ON DELETE CASCADE,
  CONSTRAINT `benevole_competence_ibfk_2` FOREIGN KEY (`competence_id`) REFERENCES `competence` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `benevole_competence` VALUES
(4,1),(5,2),(10,3),(10,4),(6,5),(12,6),(12,7);

-- ==================== DISPONIBILITÉ BÉNÉVOLE ====================
DROP TABLE IF EXISTS `benevole_disponibilite`;
CREATE TABLE `benevole_disponibilite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `benevole_id` int(11) DEFAULT NULL,
  `jour_semaine` varchar(10) DEFAULT NULL,
  `date_dispo` date DEFAULT NULL,
  `heure_debut` time DEFAULT NULL,
  `heure_fin` time DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `benevole_id` (`benevole_id`),
  CONSTRAINT `benevole_disponibilite_ibfk_1` FOREIGN KEY (`benevole_id`) REFERENCES `benevole` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `benevole_disponibilite` VALUES
(1,4,'Lundi',NULL,'08:00:00','12:00:00'),
(2,4,'Mercredi',NULL,'14:00:00','18:00:00'),
(3,5,'Mardi',NULL,'09:00:00','17:00:00'),
(4,10,'Jeudi',NULL,'10:00:00','16:00:00');

-- ==================== LIEU DE DISTRIBUTION ====================
DROP TABLE IF EXISTS `lieu_distribution`;
CREATE TABLE `lieu_distribution` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `type` varchar(50) NOT NULL,
  `adresse` text NOT NULL,
  `personne_contact` varchar(255) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `lieu_distribution` VALUES
(1,'Restos du Cœur Paris','Association','12 Rue de la Solidarité, 75001 Paris','Jean Martin','0123456789'),
(2,'Secours Populaire Paris','Association','45 Rue de l\'Entraide, 75002 Paris','Marie Dupont','0198765432'),
(4,'Banque Alimentaire Nantes','Association','3 Rue du Don, 44000 Nantes','Sophie Leclerc','0240123456');

-- ==================== COLLECTE ====================
DROP TABLE IF EXISTS `collecte`;
CREATE TABLE `collecte` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_heure_collecte` datetime NOT NULL,
  `adresse_collecte` text NOT NULL,
  `statut` varchar(20) DEFAULT 'En attente',
  `commentaire` text,
  `commercant_id` int(11) DEFAULT NULL,
  `validee` tinyint(1) DEFAULT '0',
  `nb_benevoles` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `commercant_id` (`commercant_id`),
  CONSTRAINT `collecte_ibfk_1` FOREIGN KEY (`commercant_id`) REFERENCES `commercant` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `collecte` VALUES
(1,'2026-03-10 07:30:00','10 Rue de la Paix, 75002 Paris','Terminée','Invendus du matin',2,1,0),
(8,'2026-08-15 10:00:00','12 Rue du Commerce, 44000 Nantes','Terminée','Collecte de produits bio',9,1,4),
(9,'2026-08-25 07:30:00','10 Rue de la Paix, 75002 Paris','Terminée','Collecte des invendus du matin (pain, viennoiseries)',2,1,3),
(10,'2026-08-27 18:00:00','5 Avenue Victor Hugo, 75016 Paris','Terminée','Collecte de produits frais et épicerie',3,1,4),
(20,'2026-08-21 09:45:00','5 Avenue Victor Hugo, 75016 Paris','Planifiée','Veuillez venir avec un camion',3,1,1);

-- ==================== COLLECTE <-> BÉNÉVOLE ====================
DROP TABLE IF EXISTS `collecte_benevole`;
CREATE TABLE `collecte_benevole` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `collecte_id` int(11) NOT NULL,
  `benevole_id` int(11) NOT NULL,
  `confirme` tinyint(1) DEFAULT '0',
  `date_confirmation` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `collecte_id` (`collecte_id`,`benevole_id`),
  KEY `benevole_id` (`benevole_id`),
  CONSTRAINT `collecte_benevole_ibfk_1` FOREIGN KEY (`collecte_id`) REFERENCES `collecte` (`id`) ON DELETE CASCADE,
  CONSTRAINT `collecte_benevole_ibfk_2` FOREIGN KEY (`benevole_id`) REFERENCES `benevole` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `collecte_benevole` VALUES
(1,8,4,0,NULL),(2,8,5,0,NULL),(4,8,10,0,NULL),(5,8,12,0,NULL),
(6,9,4,0,NULL),(7,9,5,0,NULL),(13,9,12,0,NULL),
(8,10,6,0,NULL),(9,10,10,0,NULL),(10,10,4,0,NULL),(11,10,5,0,NULL),
(20,20,4,0,NULL);

-- ==================== PRODUIT ====================
DROP TABLE IF EXISTS `produit`;
CREATE TABLE `produit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code_barre` varchar(50) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `categorie` varchar(100) DEFAULT NULL,
  `quantite` int(11) NOT NULL DEFAULT '1',
  `date_peremption` date NOT NULL,
  `date_entree_stock` datetime DEFAULT CURRENT_TIMESTAMP,
  `statut` varchar(20) DEFAULT 'Stocké',
  `collecte_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `collecte_id` (`collecte_id`),
  CONSTRAINT `produit_ibfk_1` FOREIGN KEY (`collecte_id`) REFERENCES `collecte` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `produit` VALUES
(2,'3760123456790','Croissants assortis','Boulangerie',30,'2026-03-11','2026-08-04 23:13:13','À distribuer',1),
(4,'3560070460096','Yaourts nature x12','Produits laitiers',8,'2026-03-20','2026-08-04 23:13:13','Distribué',1),
(6,'3760123456791','Baguettes tradition','Boulangerie',25,'2026-03-13','2026-08-04 23:13:13','À distribuer',1),
(14,'3760123456789','Pain complet','Boulangerie',20,'2026-08-20','2026-08-10 14:57:00','Stocké',8),
(15,'3017620422003','Confiture fraise','Épicerie',15,'2026-12-01','2026-08-10 14:57:00','Stocké',8),
(18,'3760123456789','Pain de campagne','Boulangerie',20,'2026-08-27','2026-08-10 16:44:32','Stocké',9),
(19,'3760123456790','Croissants assortis','Boulangerie',30,'2026-08-26','2026-08-10 16:44:32','À distribuer',9),
(20,'3560070460096','Yaourts nature x12','Produits laitiers',8,'2026-08-30','2026-08-10 16:44:32','Stocké',9),
(21,'3017620422003','Confiture fraise','Épicerie',15,'2026-12-01','2026-08-10 16:44:32','Stocké',10),
(22,'3228857000902','Salade mélangée','Frais',12,'2026-08-29','2026-08-10 16:44:32','Stocké',10),
(23,'3760123456791','Baguettes tradition','Boulangerie',25,'2026-08-28','2026-08-10 16:44:32','Stocké',10),
(24,'3560070460097','Fromage blanc x6','Produits laitiers',10,'2026-09-01','2026-08-10 16:44:32','Stocké',10),
(37,'3767732945647','Chewing-gum','Épicerie',12,'2026-08-27','2026-08-13 20:23:42','Stocké',20),
(38,'3762589954200','Soda','Épicerie',8,'2026-08-06','2026-08-13 20:23:42','Distribué',20);

-- ==================== TOURNÉE ====================
DROP TABLE IF EXISTS `tournee`;
CREATE TABLE `tournee` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_heure_depart` datetime NOT NULL,
  `date_heure_fin` datetime DEFAULT NULL,
  `adresse_depart` text NOT NULL,
  `statut` varchar(20) DEFAULT 'Prévue',
  `benevole_id` int(11) DEFAULT NULL,
  `lieu_distribution_id` int(11) DEFAULT NULL,
  `chauffeur_confirme` tinyint(1) DEFAULT '0',
  `date_confirmation_chauffeur` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `benevole_id` (`benevole_id`),
  KEY `lieu_distribution_id` (`lieu_distribution_id`),
  CONSTRAINT `tournee_ibfk_1` FOREIGN KEY (`benevole_id`) REFERENCES `benevole` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tournee_ibfk_2` FOREIGN KEY (`lieu_distribution_id`) REFERENCES `lieu_distribution` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tournee` VALUES
(1,'2026-03-11 10:00:00','2026-08-04 23:52:44','Siège NO MORE WASTE, 75001 Paris','Terminée',4,1,0,NULL),
(2,'2026-03-09 14:00:00','2026-03-09 17:30:00','Siège NO MORE WASTE, 75001 Paris','Terminée',4,2,0,NULL),
(4,'2026-08-21 11:00:00','2026-08-13 14:05:01','Siège NO MORE WASTE, 75001 Paris','Terminée',4,2,1,'2026-08-13 14:05:01');

-- ==================== TOURNÉE <-> BÉNÉVOLE ====================
DROP TABLE IF EXISTS `tournee_benevole`;
CREATE TABLE `tournee_benevole` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tournee_id` int(11) NOT NULL,
  `benevole_id` int(11) NOT NULL,
  `confirme` tinyint(1) DEFAULT '0',
  `date_confirmation` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tournee_id` (`tournee_id`,`benevole_id`),
  KEY `benevole_id` (`benevole_id`),
  CONSTRAINT `tournee_benevole_ibfk_1` FOREIGN KEY (`tournee_id`) REFERENCES `tournee` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tournee_benevole_ibfk_2` FOREIGN KEY (`benevole_id`) REFERENCES `benevole` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tournee_benevole` VALUES
(1,1,10,0,NULL),(2,2,6,0,NULL),(3,2,10,0,NULL),(8,4,6,0,NULL),(9,4,10,0,NULL);

-- ==================== TOURNÉE <-> PRODUIT ====================
DROP TABLE IF EXISTS `tournee_produit`;
CREATE TABLE `tournee_produit` (
  `tournee_id` int(11) NOT NULL,
  `produit_id` int(11) NOT NULL,
  PRIMARY KEY (`tournee_id`,`produit_id`),
  KEY `produit_id` (`produit_id`),
  CONSTRAINT `tournee_produit_ibfk_1` FOREIGN KEY (`tournee_id`) REFERENCES `tournee` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tournee_produit_ibfk_2` FOREIGN KEY (`produit_id`) REFERENCES `produit` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tournee_produit` VALUES
(2,2),(4,2),(2,6),(4,6),(1,19);

-- ==================== SERVICE ====================
DROP TABLE IF EXISTS `service`;
CREATE TABLE `service` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `description` text,
  `type` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `service` VALUES
(1,'Conseils anti-gaspillage','Ateliers pour réduire le gaspillage alimentaire','Atelier'),
(2,'Cours de cuisine','Cuisiner avec des produits de saison','Cours'),
(3,'Partage de véhicules','Mise en relation pour le covoiturage','Service'),
(4,'Échange de services','Bricolage, électricité, plomberie entre adhérents','Service'),
(5,'Services de réparation','Réparation de petits appareils','Service'),
(6,'Gardiennage','Gardiennage de domiciles','Service');

-- ==================== SERVICE <-> COMPÉTENCE ====================
DROP TABLE IF EXISTS `service_competence`;
CREATE TABLE `service_competence` (
  `service_id` int(11) NOT NULL,
  `competence_id` int(11) NOT NULL,
  PRIMARY KEY (`service_id`,`competence_id`),
  KEY `competence_id` (`competence_id`),
  CONSTRAINT `service_competence_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`) ON DELETE CASCADE,
  CONSTRAINT `service_competence_ibfk_2` FOREIGN KEY (`competence_id`) REFERENCES `competence` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==================== PLANNING SERVICE ====================
DROP TABLE IF EXISTS `service_planning`;
CREATE TABLE `service_planning` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_heure_debut` datetime NOT NULL,
  `date_heure_fin` datetime NOT NULL,
  `capacite_max` int(11) NOT NULL,
  `statut` varchar(20) DEFAULT 'Ouvert',
  `service_id` int(11) DEFAULT NULL,
  `benevole_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `service_id` (`service_id`),
  KEY `benevole_id` (`benevole_id`),
  CONSTRAINT `service_planning_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`) ON DELETE CASCADE,
  CONSTRAINT `service_planning_ibfk_2` FOREIGN KEY (`benevole_id`) REFERENCES `benevole` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `service_planning` VALUES
(12,'2026-09-05 18:00:00','2026-09-05 20:00:00',6,'Ouvert',6,6),
(13,'2026-09-19 18:00:00','2026-09-19 20:00:00',6,'Ouvert',6,10);

-- ==================== INSCRIPTION SERVICE ====================
DROP TABLE IF EXISTS `service_inscription`;
CREATE TABLE `service_inscription` (
  `adherent_id` int(11) NOT NULL,
  `service_planning_id` int(11) NOT NULL,
  `date_inscription` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`adherent_id`,`service_planning_id`),
  KEY `service_planning_id` (`service_planning_id`),
  CONSTRAINT `service_inscription_ibfk_1` FOREIGN KEY (`adherent_id`) REFERENCES `adherent` (`id`) ON DELETE CASCADE,
  CONSTRAINT `service_inscription_ibfk_2` FOREIGN KEY (`service_planning_id`) REFERENCES `service_planning` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `service_inscription` VALUES
(7,12,'2026-08-14 01:05:39');

-- ==================== RAPPEL DE RENOUVELLEMENT ====================
DROP TABLE IF EXISTS `rappel_renouvellement`;
CREATE TABLE `rappel_renouvellement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `commercant_id` int(11) DEFAULT NULL,
  `adherent_id` int(11) DEFAULT NULL,
  `date_fin_adhesion` date DEFAULT NULL,
  `date_rappel` date NOT NULL,
  `est_envoye` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `commercant_id` (`commercant_id`),
  KEY `fk_rappel_adherent` (`adherent_id`),
  CONSTRAINT `fk_rappel_adherent` FOREIGN KEY (`adherent_id`) REFERENCES `adherent` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rappel_renouvellement_ibfk_1` FOREIGN KEY (`commercant_id`) REFERENCES `commercant` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `rappel_renouvellement` VALUES
(1,NULL,11,'2026-09-12','2026-08-14',1);

-- ==================== CONVERSATION ====================
DROP TABLE IF EXISTS `conversation`;
CREATE TABLE `conversation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('admin','pair') NOT NULL DEFAULT 'admin',
  `initiateur_id` int(11) NOT NULL,
  `destinataire_id` int(11) DEFAULT NULL,
  `collecte_id` int(11) DEFAULT NULL,
  `sujet` varchar(255) NOT NULL,
  `cloturee` tinyint(1) DEFAULT '0',
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_cloture` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `initiateur_id` (`initiateur_id`),
  KEY `destinataire_id` (`destinataire_id`),
  KEY `collecte_id` (`collecte_id`),
  CONSTRAINT `conversation_ibfk_1` FOREIGN KEY (`initiateur_id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE,
  CONSTRAINT `conversation_ibfk_2` FOREIGN KEY (`destinataire_id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE,
  CONSTRAINT `conversation_ibfk_3` FOREIGN KEY (`collecte_id`) REFERENCES `collecte` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `conversation` VALUES
(1,'admin',3,NULL,NULL,'Question sur ma collecte',1,'2026-08-13 19:46:15','2026-08-13 20:22:54'),
(7,'admin',7,NULL,NULL,'Demande de gardiennage',0,'2026-08-14 01:09:36',NULL),
(9,'admin',4,NULL,NULL,'Disponibilités',0,'2026-08-14 09:33:16',NULL);

-- ==================== MESSAGE ====================
DROP TABLE IF EXISTS `message`;
CREATE TABLE `message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `conversation_id` int(11) NOT NULL,
  `expediteur_id` int(11) NOT NULL,
  `contenu` text NOT NULL,
  `date_envoi` datetime DEFAULT CURRENT_TIMESTAMP,
  `lu` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `conversation_id` (`conversation_id`),
  KEY `expediteur_id` (`expediteur_id`),
  CONSTRAINT `message_ibfk_1` FOREIGN KEY (`conversation_id`) REFERENCES `conversation` (`id`) ON DELETE CASCADE,
  CONSTRAINT `message_ibfk_2` FOREIGN KEY (`expediteur_id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `message` VALUES
(1,1,3,'Bonjour, j\'ai une question sur ma dernière collecte.','2026-08-13 19:46:15',1),
(5,1,1,'Bonjour, que puis-je faire pour vous ?','2026-08-13 20:05:48',1),
(17,7,7,'Bonjour, je souhaiterais faire une demande de gardiennage.','2026-08-14 01:09:36',1),
(21,9,4,'Voici mes disponibilités pour la semaine prochaine.','2026-08-14 09:33:16',0);

-- ==================== PARAMÈTRE ====================
DROP TABLE IF EXISTS `parametre`;
CREATE TABLE `parametre` (
  `cle` varchar(50) NOT NULL,
  `valeur` varchar(255) NOT NULL,
  PRIMARY KEY (`cle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `parametre` VALUES
('prix_adhesion_mensuel','7.99');

-- ==================== PLANNING BÉNÉVOLE ====================
DROP TABLE IF EXISTS `planning_benevole`;
CREATE TABLE `planning_benevole` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `benevole_id` int(11) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `date_generation` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `benevole_id` (`benevole_id`),
  CONSTRAINT `planning_benevole_ibfk_1` FOREIGN KEY (`benevole_id`) REFERENCES `benevole` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `planning_benevole` VALUES
(1,4,'2026-08-14','2026-08-21','2026-08-14 10:04:30');

SET FOREIGN_KEY_CHECKS = 1;