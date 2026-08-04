# Arborescence du projet NEXUS

> Projet CodeIgniter 4 — Plateforme de gestion de formations
> 3 rôles : Admin, Formateur, Étudiant

---

```
nexus/
├── app/
│   ├── Config/
│   │   ├── Database.php              ← Connexion BDD (host, db, user, password)
│   │   ├── Filters.php               ← Déclare les filtres : admin, formateur, etudiant, auth
│   │   └── Routes.php                ← Toutes les routes (public, admin, formateur, etudiant)
│   │
│   ├── Controllers/
│   │   ├── BaseController.php        ← Classe parente de tous les controllers
│   │   ├── Auth.php                  ← login, attemptLogin, logout, register, attemptRegister
│   │   ├── Home.php                  ← Page d'accueil publique + page formations publique
│   │   │
│   │   ├── Admin/
│   │   │   ├── Dashboard.php         ← Stats globales (nb formations, pôles, sessions, étudiants)
│   │   │   ├── PolesController.php   ← CRUD pôles
│   │   │   ├── FormationsController.php  ← CRUD formations + upload image
│   │   │   ├── SessionsController.php    ← CRUD sessions + liste formateurs
│   │   │   ├── InscriptionsController.php← CRUD inscriptions + approuver/refuser paiement
│   │   │   └── ComptesController.php     ← Liste comptes + bascule rôle Etudiant↔Formateur
│   │   │
│   │   ├── Formateur/
│   │   │   └── Dashboard.php         ← dashboard, planning, historique, catalogue,
│   │   │                               mesInscriptions, inscrire, envoyerPaiement
│   │   │
│   │   └── Etudiant/
│   │       └── Dashboard.php         ← dashboard, mesInscriptions, catalogue,
│   │                                   inscrire, envoyerPaiement
│   │
│   ├── Filters/
│   │   ├── AdminFilter.php           ← Vérifie isLoggedIn + role === 'Admin'
│   │   ├── FormateurFilter.php       ← Vérifie isLoggedIn + role === 'Formateur'
│   │   ├── EtudiantFilter.php        ← Vérifie isLoggedIn + role === 'Etudiant'
│   │   └── AuthFilter.php            ← Vérifie isLoggedIn (générique)
│   │
│   ├── Models/
│   │   ├── UserModel.php             ← table: comptes — findByEmail() avec priorité de rôle
│   │   ├── CompteModel.php           ← table: comptes — getAllWithRole()
│   │   ├── PoleModel.php             ← table: poles — CRUD simple
│   │   ├── FormationModel.php        ← table: formations — getAllWithPole(), getWithPole()
│   │   ├── SessionModel.php          ← table: sessions — getAllWithFormation(),
│   │   │                               getWithFormation(), getSessionsOuvertes(), getByFormateur()
│   │   └── InscriptionModel.php      ← table: inscriptions — getAllWithFormation(),
│   │                                   getByCompte(), getWithDetails(), isAlreadyInscrit(),
│   │                                   countInscriptions(), updateByKeys(), deleteByKeys()
│   │
│   ├── Helpers/
│   │   └── payement_helper.php       ← Helper pour la validation IBAN
│   │
│   └── Views/
│       ├── layouts/
│       │   └── public.php            ← Layout navbar publique (Bootstrap navbar-expand-lg)
│       │
│       ├── home/
│       │   ├── index.php             ← Page d'accueil publique
│       │   └── formation.php         ← Liste des formations publique
│       │
│       ├── auth/
│       │   ├── login.php             ← Formulaire de connexion
│       │   └── register.php          ← Formulaire d'inscription
│       │
│       ├── admin/
│       │   ├── layout.php            ← Layout admin (sidebar + topbar + hamburger mobile)
│       │   ├── dashboard.php
│       │   ├── poles/
│       │   │   ├── index.php         ← Liste + suppression AJAX
│       │   │   ├── create.php
│       │   │   └── edit.php
│       │   ├── formations/
│       │   │   ├── index.php         ← Liste + suppression AJAX
│       │   │   ├── create.php        ← Formulaire avec upload image
│       │   │   └── edit.php
│       │   ├── sessions/
│       │   │   ├── index.php         ← Liste + suppression AJAX
│       │   │   ├── create.php
│       │   │   └── edit.php
│       │   ├── inscriptions/
│       │   │   ├── index.php         ← Liste + suppression AJAX
│       │   │   ├── create.php
│       │   │   └── edit.php          ← Contient approuver/refuser paiement
│       │   └── comptes/
│       │       └── index.php         ← Liste + bouton bascule rôle
│       │
│       ├── formateur/
│       │   ├── layout.php            ← Layout formateur (même structure que admin)
│       │   ├── dashboard.php         ← Sessions assignées au formateur
│       │   ├── planning.php          ← Toutes ses sessions triées par date
│       │   └── historique.php        ← Sessions 'Terminee' + stats (heures, étudiants formés)
│       │
│       └── etudiant/
│           ├── layout.php            ← Layout étudiant (même structure que admin)
│           ├── dashboard.php
│           ├── catalogue.php         ← Sessions ouvertes — PARTAGÉE avec formateur
│           └── mes_inscriptions.php  ← Inscriptions + paiement IBAN — PARTAGÉE avec formateur
│
└── public/
    ├── index.php                     ← Point d'entrée CI4
    ├── img/
    │   ├── Logo_Nexus.png
    │   ├── facebook.png
    │   ├── instagram.png
    │   └── twitter.png
    └── uploads/
        └── formations/               ← Images uploadées pour les formations
```

---

## Tables BDD (toutes en minuscules)

| Table            | Clé primaire           | Colonnes principales                                              |
|------------------|------------------------|-------------------------------------------------------------------|
| `comptes`        | `id_compte`            | nom, prenom, email, password, adresse, code_postal, date_naissance, photo_profil |
| `roles`          | `id_role`              | libelle (Admin / Formateur / Etudiant)                            |
| `compte_roles`   | `id_compte + id_role`  | date_attribution                                                  |
| `poles`          | `id_pole`              | nom_pole, description                                             |
| `formations`     | `id_formation`         | titre, description, duree, prix, image, prerequis, id_pole        |
| `sessions`       | `id_session`           | id_formation, id_formateur, date_debut, date_fin, modalite, statut, place_max, lieu, lien_visio |
| `inscriptions`   | `id_compte + id_session` | etat_parcours, note_admin, date_inscription, date_envoi_paiement, date_validation_paiement, raison_refus_paiement |

---

## Statuts session
```
A_venir → Ouverte → En_cours → Terminee
                             → Annulee
```

## États inscription
```
Inscrit → En_cours → Termine
                   → Abandonne
```

---

## Priorité des rôles (UserModel::findByEmail)
En cas de comptes multi-rôles : **Admin > Formateur > Etudiant**

---

## Pattern layout (admin / formateur / etudiant)

Les vues capturent leur contenu avec `ob_start()` / `ob_get_clean()` puis appellent le layout :

```php
<?php ob_start(); ?>
<!-- contenu HTML ici -->
<?php $content = ob_get_clean(); ?>
<?php $scripts = '<script>/* DataTables, etc. */</script>'; ?>
<?= view('admin/layout', ['title' => $title, 'content' => $content, 'scripts' => $scripts]) ?>
```

---

## Vues partagées formateur / étudiant

`etudiant/catalogue.php` et `etudiant/mes_inscriptions.php` acceptent deux variables optionnelles
pour être réutilisées par le formateur sans dupliquer le code :

```php
// Dans Formateur\Dashboard
return view('etudiant/catalogue', [
    'layout'     => 'formateur/layout',  // layout formateur au lieu d'étudiant
    'base_route' => 'formateur',         // URLs formateur/inscrire au lieu d'etudiant/inscrire
    ...
]);
```
