# NEXUS — Plateforme de gestion de centre de formation

Application web full-stack de gestion pour un centre de formation, développée avec CodeIgniter 4. Gère les inscriptions, les sessions de formation et l'administration à travers quatre rôles utilisateurs distincts (Visiteur, Étudiant, Formateur, Administrateur).

![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?logo=php&logoColor=white)
![CodeIgniter](https://img.shields.io/badge/CodeIgniter-4-EF4223?logo=codeigniter&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3?logo=bootstrap&logoColor=white)
![License](https://img.shields.io/badge/license-MIT-green)

## Sommaire

- [Fonctionnalités](#fonctionnalités)
- [Stack technique](#stack-technique)
- [Points techniques notables](#points-techniques-notables)
- [Installation](#installation)
- [Structure du projet](#structure-du-projet)
- [Ce que j'ai appris](#ce-que-jai-appris)

## Fonctionnalités

- **Gestion multi-rôles** : Visiteur (catalogue public), Étudiant (inscriptions, suivi), Formateur (planning, historique), Administrateur (gestion complète)
- **Catalogue de formations** consultable publiquement, avec détails et sessions disponibles
- **Système d'inscription** avec gestion des places, sessions en présentiel ou à distance
- **Validation administrative des paiements** (virement bancaire, sans paiement en ligne)
- **Gestion des pôles, formations et sessions** côté administrateur
- **Recherche et filtres dynamiques** via AJAX/DataTables

## Stack technique

| Domaine | Technologie |
|---|---|
| Backend | PHP 8, CodeIgniter 4 (MVC) |
| Base de données | MySQL |
| Frontend | Bootstrap 5, jQuery, DataTables |
| Requêtes asynchrones | AJAX |
| Environnement de dev | WAMP |

## Points techniques notables

Quelques choix d'implémentation qui ont demandé une réflexion particulière :

- **Priorité des rôles en base de données** : quand un compte cumule plusieurs rôles, la priorité (Admin > Formateur > Étudiant) est résolue directement en SQL via `ORDER BY FIELD(...) LIMIT 1` dans `CompteModel`, plutôt que par une logique conditionnelle dans le contrôleur — un choix qui centralise la règle métier au bon endroit.
- **Gestion de clés composites** : `InscriptionModel` implémente des méthodes `updateByKeys()` / `deleteByKeys()` sur mesure, CodeIgniter 4 ne gérant pas nativement les clés primaires composites.
- **Compatibilité cross-plateforme des uploads** : les noms de fichiers uploadés sont normalisés en minuscules (`strtolower()`) pour éviter les problèmes de casse entre systèmes de fichiers (Windows en développement, Linux en production).
- **Sessions en base de données** : passage du gestionnaire de sessions CI4 par défaut vers un handler base de données pour fiabiliser la persistance de session sous WAMP.

## Installation

### Prérequis
- PHP 8.x
- MySQL
- Serveur local (WAMP, XAMPP, ou équivalent)
- Composer *(pour les évolutions futures du projet — voir note ci-dessous)*

### Étapes

```bash
# Cloner le dépôt
git clone https://github.com/Lio-Be/Nexus.git
cd Nexus

# Copier le fichier d'environnement
cp env .env
# Éditer .env avec vos identifiants de base de données locale

# Importer la base de données
# (voir le fichier SQL fourni)

# Lancer le serveur de développement
php spark serve
```

> **Note** : le projet embarque actuellement le framework CodeIgniter directement dans `system/` plutôt que via Composer/`vendor/`. Une migration vers une installation Composer standard est prévue.

## Structure du projet

```
nexus/
├── app/
│   ├── Controllers/     # Logique métier par rôle (Admin, Etudiant, Formateur)
│   ├── Models/           # Accès aux données (CompteModel, FormationModel, ...)
│   ├── Views/            # Templates par rôle
│   └── Filters/          # Contrôle d'accès par rôle
├── docs/
│   └── academique/       # Documentation de conception (méthodologie Merise, RUP)
├── public/               # Point d'entrée, assets statiques
└── system/               # Framework CodeIgniter 4
```

## Ce que j'ai appris

- Structurer une application MVC de taille réelle en solo, avec une séparation claire des responsabilités par rôle utilisateur
- Modéliser une base de données relationnelle avec la méthode Merise (MCD → MLD → MPD) et gérer les cas de clés composites qui en découlent
- Déboguer des problèmes de compatibilité cross-plateforme (casse des fichiers, gestion de sessions) rencontrés uniquement en conditions réelles
- Documenter et présenter un projet technique de façon à ce qu'il reste compréhensible et maintenable

---

*Projet développé dans le cadre d'une formation, actuellement en cours d'évolution vers une base de code plus robuste (tests automatisés, sécurité renforcée, architecture API).*
