-- ------------------------------------------------------------------
-- NEXUS — Base de données de démonstration
-- ------------------------------------------------------------------
-- Données fictives. Tous les comptes ont le mot de passe : password
-- Import : mysql -u root -p < database/nexus.sql  (ou via phpMyAdmin)
-- Puis   : php spark migrate   (crée la table api_tokens)
-- ------------------------------------------------------------------

CREATE DATABASE IF NOT EXISTS `nexus_formations` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `nexus_formations`;


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `ci_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ci_sessions` (
  `id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data` blob NOT NULL,
  KEY `ci_sessions_timestamp` (`timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `ci_sessions` WRITE;
/*!40000 ALTER TABLE `ci_sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `ci_sessions` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `compte_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `compte_roles` (
  `id_compte` int NOT NULL,
  `id_role` int NOT NULL,
  `date_attribution` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_compte`,`id_role`),
  KEY `id_role` (`id_role`),
  CONSTRAINT `compte_roles_ibfk_1` FOREIGN KEY (`id_compte`) REFERENCES `comptes` (`id_compte`) ON DELETE CASCADE,
  CONSTRAINT `compte_roles_ibfk_2` FOREIGN KEY (`id_role`) REFERENCES `roles` (`id_role`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `compte_roles` WRITE;
/*!40000 ALTER TABLE `compte_roles` DISABLE KEYS */;
INSERT INTO `compte_roles` VALUES (1,1,'2026-02-05 18:29:35');
INSERT INTO `compte_roles` VALUES (2,2,'2026-02-05 18:29:35');
INSERT INTO `compte_roles` VALUES (3,2,'2026-02-05 18:29:35');
INSERT INTO `compte_roles` VALUES (4,2,'2026-02-05 18:29:35');
INSERT INTO `compte_roles` VALUES (5,2,'2026-02-05 18:29:35');
INSERT INTO `compte_roles` VALUES (5,3,'2026-02-05 18:29:35');
INSERT INTO `compte_roles` VALUES (6,3,'2026-02-05 18:29:35');
INSERT INTO `compte_roles` VALUES (7,3,'2026-02-05 17:33:06');
INSERT INTO `compte_roles` VALUES (8,2,'2026-03-05 18:50:24');
INSERT INTO `compte_roles` VALUES (9,2,'2026-05-07 15:09:00');
INSERT INTO `compte_roles` VALUES (10,2,'2026-05-07 15:28:39');
/*!40000 ALTER TABLE `compte_roles` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `comptes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comptes` (
  `id_compte` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `code_postal` varchar(10) DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `photo_profil` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_compte`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `comptes` WRITE;
/*!40000 ALTER TABLE `comptes` DISABLE KEYS */;
INSERT INTO `comptes` VALUES (1,'Admin','Système','admin@nexus.com','$2y$10$EZcv4ZWvlF8vn5S3/C73VuDzemIzlGOZeGBdsA7a5AEGW0C/B0.WO','Avenue Louise 123','1050','1990-01-01',NULL);
INSERT INTO `comptes` VALUES (2,'Dupont','Marie','marie.dupont@test.com','$2y$10$EZcv4ZWvlF8vn5S3/C73VuDzemIzlGOZeGBdsA7a5AEGW0C/B0.WO','Rue de la Loi 50','1000','1995-03-15',NULL);
INSERT INTO `comptes` VALUES (3,'Martin','Pierre','pierre.martin@test.com','$2y$10$EZcv4ZWvlF8vn5S3/C73VuDzemIzlGOZeGBdsA7a5AEGW0C/B0.WO','Boulevard Anspach 10','1000','1992-07-22',NULL);
INSERT INTO `comptes` VALUES (4,'Lefebvre','Sophie','sophie.lefebvre@test.com','$2y$10$EZcv4ZWvlF8vn5S3/C73VuDzemIzlGOZeGBdsA7a5AEGW0C/B0.WO','Avenue de Tervuren 75','1040','1998-11-30',NULL);
INSERT INTO `comptes` VALUES (5,'Leclerc','Jean','jean.leclerc@formateur.com','$2y$10$EZcv4ZWvlF8vn5S3/C73VuDzemIzlGOZeGBdsA7a5AEGW0C/B0.WO','Rue du Commerce 45','1000','1985-04-12',NULL);
INSERT INTO `comptes` VALUES (6,'Moreau','Claire','claire.moreau@formateur.com','$2y$10$EZcv4ZWvlF8vn5S3/C73VuDzemIzlGOZeGBdsA7a5AEGW0C/B0.WO','Chaussée de Charleroi 88','1060','1987-09-18',NULL);
INSERT INTO `comptes` VALUES (7,'Bernard','Thomas','thomas.bernard@formateur.com','$2y$10$EZcv4ZWvlF8vn5S3/C73VuDzemIzlGOZeGBdsA7a5AEGW0C/B0.WO','Rue Royale 12','1000','1984-06-10',NULL);
INSERT INTO `comptes` VALUES (8,'Girard','Lucie','lucie.girard@test.com','$2y$10$EZcv4ZWvlF8vn5S3/C73VuDzemIzlGOZeGBdsA7a5AEGW0C/B0.WO','Rue Neuve 8','1000','2000-02-14',NULL);
INSERT INTO `comptes` VALUES (9,'Roux','Emma','emma.roux@test.com','$2y$10$EZcv4ZWvlF8vn5S3/C73VuDzemIzlGOZeGBdsA7a5AEGW0C/B0.WO','Place Flagey 3','1050','1996-08-01',NULL);
INSERT INTO `comptes` VALUES (10,'Petit','Hugo','hugo.petit@test.com','$2y$10$EZcv4ZWvlF8vn5S3/C73VuDzemIzlGOZeGBdsA7a5AEGW0C/B0.WO','Rue du Midi 21','1000','1991-04-17',NULL);
/*!40000 ALTER TABLE `comptes` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `formations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `formations` (
  `id_formation` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(100) NOT NULL,
  `description` text,
  `duree` int NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `prerequis` text,
  `id_pole` int NOT NULL,
  PRIMARY KEY (`id_formation`),
  KEY `id_pole` (`id_pole`),
  CONSTRAINT `formations_ibfk_1` FOREIGN KEY (`id_pole`) REFERENCES `poles` (`id_pole`) ON DELETE RESTRICT,
  CONSTRAINT `formations_chk_1` CHECK ((`duree` > 0)),
  CONSTRAINT `formations_chk_2` CHECK ((`prix` >= 0))
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `formations` WRITE;
/*!40000 ALTER TABLE `formations` DISABLE KEYS */;
INSERT INTO `formations` VALUES (1,'Introduction à PHP','Découvrez les bases du langage PHP pour le développement web backend',40,499.00,NULL,'Connaissances en HTML/CSS recommandées',1);
INSERT INTO `formations` VALUES (2,'JavaScript Avancé','Maîtrisez JavaScript ES6+, programmation asynchrone et frameworks modernes',60,699.00,NULL,'Bases de JavaScript requises',1);
INSERT INTO `formations` VALUES (3,'Design Thinking','Méthodologie de conception centrée utilisateur pour l innovation',26,399.00,'','Aucun prérequis',2);
INSERT INTO `formations` VALUES (4,'UI/UX avec Figma','Créez des interfaces utilisateur professionnelles avec Figma',32,449.00,NULL,'Connaissances en design graphique appréciées',2);
INSERT INTO `formations` VALUES (5,'Python pour Data Science','Analyse de données avec Python, Pandas, NumPy et visualisation',50,599.00,NULL,'Bases de programmation recommandées',3);
INSERT INTO `formations` VALUES (6,'Machine Learning Fondamentaux','Introduction au Machine Learning avec Scikit-learn',45,799.00,NULL,'Connaissances en Python et statistiques requises',3);
INSERT INTO `formations` VALUES (7,'SEO et référencement naturel','Optimisez votre visibilité sur les moteurs de recherche',20,299.00,NULL,'Aucun prérequis',4);
INSERT INTO `formations` VALUES (8,'Scrum Master Certification','Préparez la certification Scrum Master avec pratique intensive',35,899.00,NULL,'Expérience en gestion de projet souhaitée',5);
/*!40000 ALTER TABLE `formations` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `inscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inscriptions` (
  `id_compte` int NOT NULL,
  `id_session` int NOT NULL,
  `date_inscription` datetime DEFAULT CURRENT_TIMESTAMP,
  `etat_parcours` enum('Inscrit','En_cours','Termine','Abandonne') DEFAULT 'Inscrit',
  `note_admin` text,
  `date_envoi_paiement` datetime DEFAULT NULL,
  `date_validation_paiement` datetime DEFAULT NULL,
  `raison_refus_paiement` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id_compte`,`id_session`),
  KEY `id_session` (`id_session`),
  CONSTRAINT `inscriptions_ibfk_1` FOREIGN KEY (`id_compte`) REFERENCES `comptes` (`id_compte`) ON DELETE CASCADE,
  CONSTRAINT `inscriptions_ibfk_2` FOREIGN KEY (`id_session`) REFERENCES `sessions` (`id_session`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `inscriptions` WRITE;
/*!40000 ALTER TABLE `inscriptions` DISABLE KEYS */;
INSERT INTO `inscriptions` VALUES (2,1,'2026-02-05 18:29:36','Inscrit',NULL,NULL,NULL,'rien recu');
INSERT INTO `inscriptions` VALUES (2,4,'2026-02-05 18:29:36','Inscrit',NULL,'2026-02-05 18:29:36','2026-05-05 00:00:00',NULL);
INSERT INTO `inscriptions` VALUES (3,1,'2026-02-05 18:29:36','Inscrit',NULL,'2026-02-05 18:29:36',NULL,NULL);
INSERT INTO `inscriptions` VALUES (3,5,'2026-02-05 18:29:36','Termine',NULL,'2026-02-05 18:29:36','2026-06-18 15:48:16',NULL);
INSERT INTO `inscriptions` VALUES (4,4,'2026-02-05 18:29:36','En_cours',NULL,'2026-02-05 18:29:36',NULL,NULL);
INSERT INTO `inscriptions` VALUES (4,5,'2026-02-05 18:29:36','Abandonne',NULL,'2026-02-05 18:29:36','2026-05-05 00:00:00',NULL);
INSERT INTO `inscriptions` VALUES (7,1,'2026-05-27 12:33:32','Inscrit',NULL,'2026-05-27 12:33:41',NULL,NULL);
INSERT INTO `inscriptions` VALUES (7,3,'2026-05-27 12:35:30','Inscrit',NULL,'2026-06-18 15:47:32',NULL,NULL);
INSERT INTO `inscriptions` VALUES (8,1,'2026-05-07 14:21:37','En_cours',NULL,'2026-05-07 14:22:56','2026-05-07 14:23:25',NULL);
INSERT INTO `inscriptions` VALUES (8,3,'2026-05-07 14:38:29','En_cours',NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `inscriptions` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `poles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `poles` (
  `id_pole` int NOT NULL AUTO_INCREMENT,
  `nom_pole` varchar(100) NOT NULL,
  `description` text,
  PRIMARY KEY (`id_pole`),
  UNIQUE KEY `nom_pole` (`nom_pole`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `poles` WRITE;
/*!40000 ALTER TABLE `poles` DISABLE KEYS */;
INSERT INTO `poles` VALUES (1,'Développement Web','Formations liées au développement web (Frontend, Backend, Full-Stack)');
INSERT INTO `poles` VALUES (2,'Design & UX','Formations en design graphique, UI/UX, et créativité');
INSERT INTO `poles` VALUES (3,'Data Science','Formations en analyse de données, Machine Learning, IA');
INSERT INTO `poles` VALUES (4,'Marketing Digital','Formations en marketing digital, SEO, réseaux sociaux');
INSERT INTO `poles` VALUES (5,'Gestion de Projet','Formations en méthodologies agiles, gestion d\'équipe');
/*!40000 ALTER TABLE `poles` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id_role` int NOT NULL AUTO_INCREMENT,
  `libelle` enum('Admin','Etudiant','Formateur') NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_role`),
  UNIQUE KEY `libelle` (`libelle`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Admin','Administrateur du système - Gestion complète');
INSERT INTO `roles` VALUES (2,'Etudiant','Étudiant - Peut s inscrire aux formations');
INSERT INTO `roles` VALUES (3,'Formateur','Formateur - Peut animer des sessions et suivre des formations');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id_session` int NOT NULL AUTO_INCREMENT,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `modalite` enum('En_ligne','Presentiel') NOT NULL,
  `statut` enum('A_venir','Ouverte','En_cours','Terminee','Annulee') DEFAULT 'A_venir',
  `place_max` int NOT NULL,
  `lieu` varchar(255) DEFAULT NULL,
  `lien_visio` varchar(255) DEFAULT NULL,
  `id_formation` int NOT NULL,
  `id_formateur` int NOT NULL,
  PRIMARY KEY (`id_session`),
  KEY `id_formation` (`id_formation`),
  KEY `id_formateur` (`id_formateur`),
  CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`id_formation`) REFERENCES `formations` (`id_formation`) ON DELETE CASCADE,
  CONSTRAINT `sessions_ibfk_2` FOREIGN KEY (`id_formateur`) REFERENCES `comptes` (`id_compte`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES (1,'2026-03-15','2026-04-15','En_ligne','Ouverte',20,NULL,'https://twitch.tv',1,5);
INSERT INTO `sessions` VALUES (2,'2026-04-01','2026-05-15','Presentiel','A_venir',15,'Centre NEXUS - Rue de la Loi 50, 1000 Bruxelles',NULL,2,6);
INSERT INTO `sessions` VALUES (3,'2026-03-20','2026-04-05','En_ligne','Ouverte',25,NULL,'https://twitch.tv',3,5);
INSERT INTO `sessions` VALUES (4,'2026-01-15','2026-02-28','Presentiel','En_cours',12,'Centre NEXUS - Avenue Louise 50, 1050 Bruxelles',NULL,4,6);
INSERT INTO `sessions` VALUES (5,'2025-11-01','2025-12-20','En_ligne','Terminee',18,NULL,'https://twitch.tv',5,5);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

