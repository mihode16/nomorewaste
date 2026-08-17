
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `adherent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adherent` (
  `id` int(11) NOT NULL,
  `date_debut_adhesion` date NOT NULL,
  `date_fin_adhesion` date NOT NULL,
  `statut_adhesion` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'en_attente',
  `demande_renouvellement` tinyint(1) DEFAULT '0',
  `est_renouvele_automatiquement` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  CONSTRAINT `adherent_ibfk_1` FOREIGN KEY (`id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `adherent` WRITE;
/*!40000 ALTER TABLE `adherent` DISABLE KEYS */;
INSERT INTO `adherent` VALUES (7,'2025-01-15','2027-02-13','valide',0,0),(8,'2025-03-01','2026-02-28','valide',0,0),(11,'2025-05-31','2026-09-12','valide',0,0),(22,'2026-08-13','2027-02-13','valide',0,1),(23,'2026-08-13','2027-08-13','valide',0,0),(24,'2026-08-13','2027-09-13','valide',0,0),(30,'2026-08-14','2027-08-14','valide',0,0);
/*!40000 ALTER TABLE `adherent` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `benevole`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `benevole` (
  `id` int(11) NOT NULL,
  `date_candidature` datetime DEFAULT CURRENT_TIMESTAMP,
  `statut_candidature` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'En attente',
  PRIMARY KEY (`id`),
  CONSTRAINT `benevole_ibfk_1` FOREIGN KEY (`id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `benevole` WRITE;
/*!40000 ALTER TABLE `benevole` DISABLE KEYS */;
INSERT INTO `benevole` VALUES (4,'2026-08-04 23:13:11','Validé'),(5,'2026-08-04 23:13:11','Refusé'),(6,'2026-08-04 23:13:11','Validé'),(10,'2026-08-04 23:13:11','Validé'),(12,'2026-08-04 23:53:33','En attente'),(20,'2026-08-12 15:55:30','Validé'),(38,'2026-08-14 11:33:09','Validé');
/*!40000 ALTER TABLE `benevole` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `benevole_competence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `benevole_competence` (
  `benevole_id` int(11) NOT NULL,
  `competence_id` int(11) NOT NULL,
  PRIMARY KEY (`benevole_id`,`competence_id`),
  KEY `competence_id` (`competence_id`),
  CONSTRAINT `benevole_competence_ibfk_1` FOREIGN KEY (`benevole_id`) REFERENCES `benevole` (`id`) ON DELETE CASCADE,
  CONSTRAINT `benevole_competence_ibfk_2` FOREIGN KEY (`competence_id`) REFERENCES `competence` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `benevole_competence` WRITE;
/*!40000 ALTER TABLE `benevole_competence` DISABLE KEYS */;
INSERT INTO `benevole_competence` VALUES (4,1),(5,2),(38,2),(10,3),(10,4),(6,5),(20,5),(38,5),(12,6),(12,7);
/*!40000 ALTER TABLE `benevole_competence` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `benevole_disponibilite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `benevole_disponibilite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `benevole_id` int(11) DEFAULT NULL,
  `jour_semaine` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_dispo` date DEFAULT NULL,
  `heure_debut` time DEFAULT NULL,
  `heure_fin` time DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `benevole_id` (`benevole_id`),
  CONSTRAINT `benevole_disponibilite_ibfk_1` FOREIGN KEY (`benevole_id`) REFERENCES `benevole` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `benevole_disponibilite` WRITE;
/*!40000 ALTER TABLE `benevole_disponibilite` DISABLE KEYS */;
INSERT INTO `benevole_disponibilite` VALUES (1,4,'Lundi',NULL,'08:00:00','12:00:00'),(2,4,'Mercredi',NULL,'14:00:00','18:00:00'),(3,5,'Mardi',NULL,'09:00:00','17:00:00'),(4,10,'Jeudi',NULL,'10:00:00','16:00:00'),(10,4,'Mardi','2026-08-18','09:00:00','15:00:00'),(11,4,'Vendredi','2026-08-14','09:35:00','15:35:00');
/*!40000 ALTER TABLE `benevole_disponibilite` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `collecte`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `collecte` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_heure_collecte` datetime NOT NULL,
  `adresse_collecte` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `statut` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'En attente',
  `commentaire` text COLLATE utf8mb4_unicode_ci,
  `commercant_id` int(11) DEFAULT NULL,
  `validee` tinyint(1) DEFAULT '0',
  `nb_benevoles` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `commercant_id` (`commercant_id`),
  CONSTRAINT `collecte_ibfk_1` FOREIGN KEY (`commercant_id`) REFERENCES `commercant` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `collecte` WRITE;
/*!40000 ALTER TABLE `collecte` DISABLE KEYS */;
INSERT INTO `collecte` VALUES (1,'2026-03-10 07:30:00','10 Rue de la Paix, 75002 Paris','Terminée','Invendus du matin',2,1,0),(3,'2026-03-08 12:00:00','10 Rue de la Paix, 75002 Paris','Terminée',NULL,2,1,0),(7,'2026-08-10 09:30:00','15 Rue de la Paix, 75002 Paris','Terminée','Collecte de produits frais',2,1,0),(8,'2026-08-15 10:00:00','12 Rue du Commerce, 44000 Nantes','Terminée','Collecte de produits bio',9,1,4),(9,'2026-08-25 07:30:00','10 Rue de la Paix, 75002 Paris','Terminée','Collecte des invendus du matin (pain, viennoiseries)',2,1,3),(10,'2026-08-27 18:00:00','5 Avenue Victor Hugo, 75016 Paris','Terminée','Collecte de produits frais et épicerie',3,1,4),(13,'2026-08-27 19:30:00','12 Rue du Commerce, 44000 Nantes','Terminée','test',3,1,1),(14,'2026-08-20 19:54:00','12 Rue du Commerce, 44000 Nantes','Terminée','ok',3,1,0),(20,'2026-08-21 09:45:00','5 Avenue Victor Hugo, 75016 Pari','Planifiée','Veuillez venir avec un camion',3,1,1);
/*!40000 ALTER TABLE `collecte` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `collecte_benevole`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `collecte_benevole` WRITE;
/*!40000 ALTER TABLE `collecte_benevole` DISABLE KEYS */;
INSERT INTO `collecte_benevole` VALUES (1,8,4,0,NULL),(2,8,5,0,NULL),(4,8,10,0,NULL),(5,8,12,0,NULL),(6,9,4,0,NULL),(7,9,5,0,NULL),(8,10,6,0,NULL),(9,10,10,0,NULL),(10,10,4,0,NULL),(11,10,5,0,NULL),(13,9,12,0,NULL),(18,13,6,0,NULL),(20,20,4,0,NULL);
/*!40000 ALTER TABLE `collecte_benevole` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `commercant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commercant` (
  `id` int(11) NOT NULL,
  `siret` varchar(14) COLLATE utf8mb4_unicode_ci NOT NULL,
  `raison_sociale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_commerce` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_debut_adhesion` date NOT NULL,
  `date_fin_adhesion` date NOT NULL,
  `est_renouvele_automatiquement` tinyint(1) DEFAULT '0',
  `statut_adhesion` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'en_attente',
  `demande_renouvellement` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `siret` (`siret`),
  CONSTRAINT `commercant_ibfk_1` FOREIGN KEY (`id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `commercant` WRITE;
/*!40000 ALTER TABLE `commercant` DISABLE KEYS */;
INSERT INTO `commercant` VALUES (2,'12345678901234','Boulangerie Dupont','Alimentaire','2024-12-28','2028-08-05',1,'valide',0),(3,'98765432109876','Supermarché Martin','Test','2024-05-29','2028-08-13',0,'valide',0),(9,'45678901234567','Épicerie Bio Nantes','Alimentaire','2025-02-28','2026-02-27',0,'valide',0),(18,'88565445168905','Mira Location','Location de Voiture','2026-08-05','2027-08-05',0,'valide',0),(19,'87643289102547','Chacha Patisserie','Boulangerie','2026-08-05','2027-08-05',0,'valide',0),(40,'87643289102540','Chachas Patisserie','Alimentaire','2026-08-14','2027-08-14',0,'valide',0),(41,'87643289102543','Chachai Patisserie','Alimentaire','2026-08-14','2027-08-14',0,'en_attente',0);
/*!40000 ALTER TABLE `commercant` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `competence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `competence` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `competence` WRITE;
/*!40000 ALTER TABLE `competence` DISABLE KEYS */;
INSERT INTO `competence` VALUES (1,'Chauffeur','Permis B, conduite de véhicule utilitaire'),(2,'Cuisinier','Préparation de repas et ateliers cuisine'),(3,'Plombier','Réparations et travaux de plomberie'),(4,'Electricien','Réparations et travaux d\'électricité'),(5,'Bricoleur','Travaux de bricolage divers'),(6,'Jardinier','Entretien des espaces verts'),(7,'Informaticien','Aide à l\'informatique et aux logiciels'),(8,'Animateur','Animation d\'ateliers et d\'événements');
/*!40000 ALTER TABLE `competence` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `conversation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `conversation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('admin','pair') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'admin',
  `initiateur_id` int(11) NOT NULL,
  `destinataire_id` int(11) DEFAULT NULL,
  `collecte_id` int(11) DEFAULT NULL,
  `sujet` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `conversation` WRITE;
/*!40000 ALTER TABLE `conversation` DISABLE KEYS */;
INSERT INTO `conversation` VALUES (1,'admin',3,NULL,NULL,'chien',1,'2026-08-13 19:46:15','2026-08-13 20:22:54'),(6,'pair',7,24,NULL,'chien',0,'2026-08-13 21:47:51',NULL),(7,'admin',7,NULL,NULL,'Demande de gardiennage',0,'2026-08-14 01:09:36',NULL),(9,'admin',4,NULL,NULL,'chien',0,'2026-08-14 09:33:16',NULL);
/*!40000 ALTER TABLE `conversation` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `lieu_distribution`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lieu_distribution` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `adresse` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `personne_contact` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telephone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `lieu_distribution` WRITE;
/*!40000 ALTER TABLE `lieu_distribution` DISABLE KEYS */;
INSERT INTO `lieu_distribution` VALUES (1,'Restos du Cœur Paris','Association','12 Rue de la Solidarité, 75001 Paris','Jean Martin','0123456789'),(2,'Secours Populaire Paris','Association','45 Rue de l\'Entraide, 75002 Paris','Marie Dupont','0198765432'),(3,'Centre d\'Hébergement Saint-Jean','Association','8 Rue de l\'Espoir, 75003 Paris','Pierre Bernard','0145678912'),(4,'Banque Alimentaire Nantes','Association','3 Rue du Don, 44000 Nantes','Sophie Leclerc','0240123456'),(5,'Croix-Rouge Marseille','Association','10 Avenue de l\'Aide, 13001 Marseille','Luc Moreau','0440987654'),(6,'SOS Enfance','Association','12 Rue de  la Paix, 75012 Paris',NULL,NULL);
/*!40000 ALTER TABLE `lieu_distribution` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `conversation_id` int(11) NOT NULL,
  `expediteur_id` int(11) NOT NULL,
  `contenu` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_envoi` datetime DEFAULT CURRENT_TIMESTAMP,
  `lu` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `conversation_id` (`conversation_id`),
  KEY `expediteur_id` (`expediteur_id`),
  CONSTRAINT `message_ibfk_1` FOREIGN KEY (`conversation_id`) REFERENCES `conversation` (`id`) ON DELETE CASCADE,
  CONSTRAINT `message_ibfk_2` FOREIGN KEY (`expediteur_id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `message` WRITE;
/*!40000 ALTER TABLE `message` DISABLE KEYS */;
INSERT INTO `message` VALUES (1,1,3,'voilà','2026-08-13 19:46:15',1),(5,1,1,'Y\'a quoi ?','2026-08-13 20:05:48',1),(6,1,3,'rien','2026-08-13 20:06:45',1),(7,1,1,'Et alors ??','2026-08-13 20:21:05',1),(16,6,7,'ça va ??','2026-08-13 21:47:51',1),(17,7,7,'blablabla','2026-08-14 01:09:36',1),(18,6,24,'Oui ma belle et toi','2026-08-14 01:12:04',0),(21,9,4,'C\'est moi ohh','2026-08-14 09:33:16',0);
/*!40000 ALTER TABLE `message` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `parametre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parametre` (
  `cle` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valeur` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`cle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `parametre` WRITE;
/*!40000 ALTER TABLE `parametre` DISABLE KEYS */;
INSERT INTO `parametre` VALUES ('prix_adhesion_mensuel','7.99');
/*!40000 ALTER TABLE `parametre` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `planning_benevole`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `planning_benevole` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `benevole_id` int(11) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `date_generation` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `benevole_id` (`benevole_id`),
  CONSTRAINT `planning_benevole_ibfk_1` FOREIGN KEY (`benevole_id`) REFERENCES `benevole` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `planning_benevole` WRITE;
/*!40000 ALTER TABLE `planning_benevole` DISABLE KEYS */;
INSERT INTO `planning_benevole` VALUES (3,4,'2026-08-14','2026-08-21','2026-08-14 10:04:30'),(4,4,'2026-08-14','2026-08-14','2026-08-14 14:52:42'),(5,4,'2026-08-14','2026-08-14','2026-08-14 14:52:53'),(6,4,'2026-08-14','2026-08-14','2026-08-14 15:58:36');
/*!40000 ALTER TABLE `planning_benevole` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `produit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `produit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code_barre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `categorie` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantite` int(11) NOT NULL DEFAULT '1',
  `date_peremption` date NOT NULL,
  `date_entree_stock` datetime DEFAULT CURRENT_TIMESTAMP,
  `statut` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'Stocké',
  `collecte_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `collecte_id` (`collecte_id`),
  CONSTRAINT `produit_ibfk_1` FOREIGN KEY (`collecte_id`) REFERENCES `collecte` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `produit` WRITE;
/*!40000 ALTER TABLE `produit` DISABLE KEYS */;
INSERT INTO `produit` VALUES (2,'3760123456790','Croissants assortis','Boulangerie',30,'2026-03-11','2026-08-04 23:13:13','À distribuer',1),(3,'3017620422003','Confiture fraise','Épicerie',15,'2026-08-01','2026-08-04 23:13:13','À distribuer',3),(4,'3560070460096','Yaourts nature x12','Produits laitiers',8,'2026-03-20','2026-08-04 23:13:13','Distribué',1),(6,'3760123456791','Baguettes tradition','Boulangerie',25,'2026-03-13','2026-08-04 23:13:13','À distribuer',1),(7,'3017620422004','Pâtes bio 500g','Épicerie',40,'2027-01-01','2026-08-04 23:13:13','Stocké',3),(8,'3560070460097','Fromage blanc x6','Produits laitiers',10,'2026-03-18','2026-08-04 23:13:13','Distribué',1),(9,'3760123456789','Pain de campagne','Boulangerie',20,'2026-08-20','2026-08-10 12:33:50','À distribuer',7),(10,'3760123456790','Croissants assortis','Boulangerie',30,'2026-08-18','2026-08-10 12:33:50','Stocké',7),(11,'3017620422003','Confiture fraise','Épicerie',15,'2026-12-01','2026-08-10 12:33:50','Stocké',7),(12,'3560070460096','Yaourts nature x12','Produits laitiers',8,'2026-08-25','2026-08-10 12:33:50','À distribuer',7),(14,'3760123456789','Pain complet','Boulangerie',20,'2026-08-20','2026-08-10 14:57:00','Stocké',8),(15,'3017620422003','Confiture fraise','Épicerie',15,'2026-12-01','2026-08-10 14:57:00','Stocké',8),(17,'3766372316206','Saucisse de toulouse','Charcuterie',3,'2028-02-10','2026-08-10 16:13:44','Stocké',8),(18,'3760123456789','Pain de campagne','Boulangerie',20,'2026-08-27','2026-08-10 16:44:32','Stocké',9),(19,'3760123456790','Croissants assortis','Boulangerie',30,'2026-08-26','2026-08-10 16:44:32','À distribuer',9),(20,'3560070460096','Yaourts nature x12','Produits laitiers',8,'2026-08-30','2026-08-10 16:44:32','Stocké',9),(21,'3017620422003','Confiture fraise','Épicerie',15,'2026-12-01','2026-08-10 16:44:32','Stocké',10),(22,'3228857000902','Salade mélangée','Frais',12,'2026-08-29','2026-08-10 16:44:32','Stocké',10),(23,'3760123456791','Baguettes tradition','Boulangerie',25,'2026-08-28','2026-08-10 16:44:32','Stocké',10),(24,'3560070460097','Fromage blanc x6','Produits laitiers',10,'2026-09-01','2026-08-10 16:44:32','Stocké',10),(25,'3768853000604','Pain au chocolat','Bonlangerie',1,'2026-08-29','2026-08-10 23:48:31','Stocké',9),(26,'3766118300186','Produit test','Test',3,'2026-12-01','2026-08-13 18:19:06','Stocké',NULL),(28,'3769808890806','Haricots verts','Légumes',15,'2029-06-16','2026-08-13 18:20:13','Stocké',14),(31,'3765770424018','Haricots verts','Légumes',1,'2029-06-16','2026-08-13 18:27:51','Stocké',NULL),(37,'3767732945647','cheewingum','Épicerie',12,'2026-08-27','2026-08-13 20:23:42','Stocké',20),(38,'3762589954200','soda','Épicerie',8,'2026-08-06','2026-08-13 20:23:42','Distribué',20);
/*!40000 ALTER TABLE `produit` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `rappel_renouvellement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `rappel_renouvellement` WRITE;
/*!40000 ALTER TABLE `rappel_renouvellement` DISABLE KEYS */;
INSERT INTO `rappel_renouvellement` VALUES (3,NULL,11,'2026-09-12','2026-08-14',1);
/*!40000 ALTER TABLE `rappel_renouvellement` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `service` WRITE;
/*!40000 ALTER TABLE `service` DISABLE KEYS */;
INSERT INTO `service` VALUES (1,'Conseils anti-gaspillage','Ateliers pour réduire le gaspillage alimentaire','Atelier'),(2,'Cours de cuisine','Cuisiner avec des produits de saison','Cours'),(3,'Partage de véhicules','Mise en relation pour le covoiturage','Service'),(4,'Échange de services','Bricolage, électricité, plomberie entre adhérents','Service'),(5,'Services de réparation','Réparation de petits appareils','Service'),(6,'Gardiennage','Gardiennage de domiciles','Service'),(11,'Atelier compostage','Apprenez à composter vos déchets organiques et à réduire votre poubelle au quotidien.','Atelier'),(12,'Formation zéro déchet','Formation pratique pour adopter les bons réflexes zéro déchet à la maison et au travail.','Formation'),(13,'Distribution solidaire','Distribution de paniers de produits collectés, réservée aux adhérents de l\'association.','Service');
/*!40000 ALTER TABLE `service` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `service_competence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service_competence` (
  `service_id` int(11) NOT NULL,
  `competence_id` int(11) NOT NULL,
  PRIMARY KEY (`service_id`,`competence_id`),
  KEY `competence_id` (`competence_id`),
  CONSTRAINT `service_competence_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`) ON DELETE CASCADE,
  CONSTRAINT `service_competence_ibfk_2` FOREIGN KEY (`competence_id`) REFERENCES `competence` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `service_competence` WRITE;
/*!40000 ALTER TABLE `service_competence` DISABLE KEYS */;
/*!40000 ALTER TABLE `service_competence` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `service_inscription`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service_inscription` (
  `adherent_id` int(11) NOT NULL,
  `service_planning_id` int(11) NOT NULL,
  `date_inscription` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`adherent_id`,`service_planning_id`),
  KEY `service_planning_id` (`service_planning_id`),
  CONSTRAINT `service_inscription_ibfk_1` FOREIGN KEY (`adherent_id`) REFERENCES `adherent` (`id`) ON DELETE CASCADE,
  CONSTRAINT `service_inscription_ibfk_2` FOREIGN KEY (`service_planning_id`) REFERENCES `service_planning` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `service_inscription` WRITE;
/*!40000 ALTER TABLE `service_inscription` DISABLE KEYS */;
INSERT INTO `service_inscription` VALUES (7,9,'2026-08-14 01:05:39'),(24,14,'2026-08-14 09:37:33'),(30,9,'2026-08-14 11:19:42');
/*!40000 ALTER TABLE `service_inscription` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `service_planning`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service_planning` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_heure_debut` datetime NOT NULL,
  `date_heure_fin` datetime NOT NULL,
  `capacite_max` int(11) NOT NULL,
  `statut` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'Ouvert',
  `service_id` int(11) DEFAULT NULL,
  `benevole_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `service_id` (`service_id`),
  KEY `benevole_id` (`benevole_id`),
  CONSTRAINT `service_planning_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`) ON DELETE CASCADE,
  CONSTRAINT `service_planning_ibfk_2` FOREIGN KEY (`benevole_id`) REFERENCES `benevole` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `service_planning` WRITE;
/*!40000 ALTER TABLE `service_planning` DISABLE KEYS */;
INSERT INTO `service_planning` VALUES (9,'2026-08-25 10:00:00','2026-08-25 12:00:00',12,'Ouvert',11,12),(10,'2026-09-15 09:00:00','2026-09-15 11:00:00',8,'Ouvert',11,4),(11,'2026-08-29 14:00:00','2026-08-29 17:00:00',15,'Ouvert',12,5),(12,'2026-09-05 18:00:00','2026-09-05 20:00:00',6,'Ouvert',13,6),(13,'2026-09-19 18:00:00','2026-09-19 20:00:00',6,'Ouvert',13,10),(14,'2026-08-14 11:35:00','2026-08-14 11:37:00',1,'Ouvert',3,4);
/*!40000 ALTER TABLE `service_planning` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `tournee`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tournee` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_heure_depart` datetime NOT NULL,
  `date_heure_fin` datetime DEFAULT NULL,
  `adresse_depart` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `statut` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'Prévue',
  `benevole_id` int(11) DEFAULT NULL,
  `lieu_distribution_id` int(11) DEFAULT NULL,
  `chauffeur_confirme` tinyint(1) DEFAULT '0',
  `date_confirmation_chauffeur` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `benevole_id` (`benevole_id`),
  KEY `lieu_distribution_id` (`lieu_distribution_id`),
  CONSTRAINT `tournee_ibfk_1` FOREIGN KEY (`benevole_id`) REFERENCES `benevole` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tournee_ibfk_2` FOREIGN KEY (`lieu_distribution_id`) REFERENCES `lieu_distribution` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `tournee` WRITE;
/*!40000 ALTER TABLE `tournee` DISABLE KEYS */;
INSERT INTO `tournee` VALUES (1,'2026-03-11 10:00:00','2026-08-04 23:52:44','Siège NO MORE WASTE, 75001 Paris','Terminée',4,1,0,NULL),(2,'2026-03-09 14:00:00','2026-03-09 17:30:00','Siège NO MORE WASTE, 75001 Paris','Terminée',4,2,0,NULL),(3,'2026-08-20 12:00:00','2026-08-13 13:53:18','Siège NO MORE WASTE, 75001 Paris','Terminée',4,6,0,NULL),(4,'2026-08-21 11:00:00','2026-08-13 14:05:01','Siège NO MORE WASTE, 75001 Paris','Terminée',4,6,1,'2026-08-13 14:05:01'),(5,'2026-08-22 11:00:00','2026-08-13 17:43:00','Siege NO MORE WASTE, 75001 Paris','Terminée',4,1,0,NULL),(6,'2026-08-14 09:49:00','2026-08-14 09:49:24','Siège NO MORE WASTE, 75001 Paris','Terminée',4,6,1,'2026-08-14 09:49:24');
/*!40000 ALTER TABLE `tournee` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `tournee_benevole`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `tournee_benevole` WRITE;
/*!40000 ALTER TABLE `tournee_benevole` DISABLE KEYS */;
INSERT INTO `tournee_benevole` VALUES (1,1,10,0,NULL),(2,2,6,0,NULL),(3,2,10,0,NULL),(5,3,6,0,NULL),(8,4,6,0,NULL),(9,4,10,0,NULL),(10,5,6,0,NULL);
/*!40000 ALTER TABLE `tournee_benevole` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `tournee_produit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tournee_produit` (
  `tournee_id` int(11) NOT NULL,
  `produit_id` int(11) NOT NULL,
  PRIMARY KEY (`tournee_id`,`produit_id`),
  KEY `produit_id` (`produit_id`),
  CONSTRAINT `tournee_produit_ibfk_1` FOREIGN KEY (`tournee_id`) REFERENCES `tournee` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tournee_produit_ibfk_2` FOREIGN KEY (`produit_id`) REFERENCES `produit` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `tournee_produit` WRITE;
/*!40000 ALTER TABLE `tournee_produit` DISABLE KEYS */;
INSERT INTO `tournee_produit` VALUES (2,2),(4,2),(4,3),(3,4),(5,4),(2,6),(4,6),(3,8),(1,9),(2,12),(1,19),(6,38);
/*!40000 ALTER TABLE `tournee_produit` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `utilisateur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `utilisateur` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mot_de_passe` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adresse` text COLLATE utf8mb4_unicode_ci,
  `date_inscription` datetime DEFAULT CURRENT_TIMESTAMP,
  `est_actif` tinyint(1) DEFAULT '1',
  `langue_preferee` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT 'fr',
  `type_utilisateur` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `utilisateur` WRITE;
/*!40000 ALTER TABLE `utilisateur` DISABLE KEYS */;
INSERT INTO `utilisateur` VALUES (1,'admin@nomorewaste.org','$2a$10$eFF3X.MLxZFaZHJhZk96CeWBYZhCFyVodcGWHEowJh/xYmLFS9JDm','Admin','NO MORE WASTE',NULL,NULL,'2026-08-04 23:13:10',1,'fr','responsable'),(2,'boulangerie.dupont@mail.fr','$2a$10$wFTk123nt57wHrjnHZngFePfOJ5IEfqlExD4iHHXCVGZIIpmFUvo.','Dupont','Claire','0611223344','10 Rue de la Paix, 75002 Paris','2026-08-04 23:13:10',0,'fr','commercant'),(3,'supermarche.martin@mail.fr','$2a$10$5LdMPoN1XLhdPisUBM9s0u6Dtw3N1.mkFz3wyhFzY9ERxmBPQoTWu','Gracesse','Paul','0699887766','5 Avenue Victor Hugo, 75016 Paris','2026-08-04 23:13:10',1,'fr','commercant'),(4,'jean.chauffeur@mail.fr','$2a$10$OoQkttQHoQ65ZzshQwgGde17v4M626s67EW7kUYyutS.Wiwsqsby6','Bernard','Jean','','','2026-08-04 23:13:11',1,'fr','benevole'),(5,'marie.cuisine@mail.fr','$2a$10$bjEEGxaXqGQOzXv0xRvzde7b7HcKSvsV.nwjomyulIL2XFytHTtfS','Leroy','Marie','0709080706',NULL,'2026-08-04 23:13:11',1,'fr','benevole'),(6,'pierre.attente@mail.fr','$2a$10$AjVZZNvXzy8W5ISgGmO4aeZs5sn3a6O7LvoThMW6ZcWNWPzKVdwea','Petit','Pierre','0612131415',NULL,'2026-08-04 23:13:11',1,'fr','benevole'),(7,'sophie.adherent@mail.fr','$2a$10$xTrSLTMJ5HdCZixPjtAaUuNoVOTbex4Dsa7thcRAEn9w6OaFNul0W','Moreau','Sophie','0611223344','172 Rue de Paris, Appartement H4729','2026-08-04 23:13:12',1,'fr','adherent'),(8,'luc.adherent@mail.fr','$2a$10$/lNQsie1OGSCz3ewB.F.ueyPCxEpYLD9okWpox5UREKlVMm.5AXEa','Girard','Luc','0699887766',NULL,'2026-08-04 23:13:12',1,'fr','adherent'),(9,'epicerie.nantes@mail.fr','$2a$10$lh9ZJlbe/oJMmW0ZzUGxzuaMf/TN/kwEB14ihSyYYIWJEur.OxvV.','Rousseau','Anne','0612345678','12 Rue du Commerce, 44000 Nantes','2026-08-04 23:13:10',1,'fr','commercant'),(10,'sarah.plombier@mail.fr','$2a$10$yjDFwvmyMjtR6Q7X/yScfevPh.x5qm/03GtRMG3LLKMepgdiFPxk6','Durand','Sarah','0622334455',NULL,'2026-08-04 23:13:11',1,'fr','benevole'),(11,'emma.adherent@mail.fr','$2a$10$KZK8FpzOD2MxuKlEW263p.M7hY33I4VuD02Ggk0OjKP6APfnxg3kO','Blanc','Emma','0677889900','','2026-08-04 23:13:12',1,'fr','adherent'),(12,'gracesse.hounkanrin@carene.fr','$2a$10$w24FwRDXP/KLcYZ95k3KBeSkIbwzEfJXErdyDPifnmcXopiQBxiku','HOUNKANRIN','Gracesse','0618278887','172 Rue de Paris, Appartement H4729','2026-08-04 23:53:33',1,'fr','benevole'),(18,'emiliendupont@gmail.com','$2a$10$F4gL6ukPMfA92Ja3tuqDsuYD7lJhPH5Y3BiQJW/w17kL1hn3S1DNO','hkn','mira','0715223378','172 Rue de Paris, Appartement H4729','2026-08-05 15:47:46',0,'fr','commercant'),(19,'lolo@gmail.com','$2a$10$kMqREMZ4G1p0Yll9mbBEMOSwh9BFmqQcRkj.6L9YPYQmrGcTuZISW','wanyinou','mihode','','22 rue des marteuax','2026-08-05 16:06:13',0,'fr','commercant'),(20,'jayatahouessou@gmail.com','$2a$10$l8PjdiCwauPkxG5mWNlUzOufGUOqpg7/VNlZFJUrqg7VaM6bMp1aq','wanyinou','mihode','0718278586','La rue à ta mère','2026-08-12 15:55:30',0,'fr','benevole'),(22,'test.membre@example.com','$2a$10$2C89fP1JHLtS.sTCto0xlej03dQZiCXrnGQygFf64Gg7Kd8saLZxC','Test','Membre Modifie','0600000002','2 rue test','2026-08-13 16:01:51',0,'fr','adherent'),(23,'nouveau.membre@example.com','$2a$10$oan6TobIzVXGao8iVseZ2ubw0kuLM7B0RdkVZA9Q75oHMYnTSmcIW','Durand','Alice Modifiee','0611112222','20 rue modifiee','2026-08-13 16:05:51',0,'fr','adherent'),(24,'mirahounkanrin@gmail.com','$2a$10$eByg3PbUfoS6R13Lq1ibNue6gORRb33eFhXN6Js8f9ncSZDFtKQRy','HOUNKANRIN','Gracesse','0618278887','172 Rue de Paris, Appartement H4729','2026-08-13 16:11:51',1,'fr','adherent'),(25,'test.admin@example.com','$2a$10$vW2tIWA2jQUu9pZI7K4ASOJM/dSD.zAc58wxn1dEx4QiMo.duRQlm','Admin','Test Modifie','0611112222','1 rue admin','2026-08-13 16:26:54',0,'fr','responsable'),(26,'nouvel.admin@example.com','$2a$10$1WJxnVhgr2zXc.dtaoofjubrlubs1hqp6mqb2YlyLk0TXBjIFQKHG','Nouveau','Admin Modifie','0688888888','1 rue admin','2026-08-13 16:30:29',0,'fr','responsable'),(27,'francinepampana@gmail.com','$2a$10$JP8iswmEwGZmIA3qkVfYbe4Z/WDHJPMGmf9q0v2iTsdkMNVbFL06W','Pampana','Francine','0767722240','Quelque part à Damartin','2026-08-13 16:34:53',1,'fr','responsable'),(30,'test@gmail.com','$2a$10$h0KjckAAepPgZbh9618tguMw8l7WFH8aYm0RLG9d1PnIx8cWXruLC','test','test','','okayyy','2026-08-14 11:12:52',1,'fr','adherent'),(38,'testo@gmail.com','$2a$10$YCBsJU7L5yv4axCBqY7nqeYoGFzxombUC/3BsPDRHla5BsCfkuR6e','test','test','','ouiii','2026-08-14 11:33:09',1,'fr','benevole'),(40,'testa@gmail.com','$2a$10$AGn9uzMJvBCR8X7haEzNB.xY73QLnaESUZ5ZKk9AnrsmXmi2aV95S','test','test','','cc','2026-08-14 11:37:58',1,'fr','commercant'),(41,'testi@gmail.com','$2a$10$Hf2.1La3BiDVYMigvbpXPeYL4tHqQDJVaXkefv0gOLMjTyh1IaS0u','test','test','','caca','2026-08-14 11:46:29',1,'fr','commercant');
/*!40000 ALTER TABLE `utilisateur` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

