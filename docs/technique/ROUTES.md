# Routes, Filtres et Sécurisation

> Explication simple avec des exemples tirés du projet NEXUS.

---

## 1. C'est quoi une route ?

Une route, c'est le lien entre une **URL** et une **méthode de contrôleur**.

Quand quelqu'un tape une URL dans le navigateur ou soumet un formulaire,
CI4 regarde dans `Routes.php` : "qui gère cette URL ?"

```php
// Quand l'utilisateur va sur /etudiant/catalogue
// → CI4 appelle la méthode catalogue() du contrôleur Etudiant\Dashboard
$routes->get('etudiant/catalogue', 'Etudiant\Dashboard::catalogue');
```

---

## 2. GET vs POST — pourquoi deux méthodes ?

### GET — afficher quelque chose

- Utilisé pour **lire** des données (afficher une page, une liste)
- Les paramètres sont visibles dans l'URL : `/admin/poles/edit/3`
- Peut être mis en favoris, partagé, rechargé sans risque

```php
// Afficher la liste des pôles
$routes->get('poles', 'Admin\PolesController::index');

// Afficher le formulaire d'édition du pôle n°3
$routes->get('poles/edit/(:num)', 'Admin\PolesController::edit/$1');

// Supprimer un pôle via AJAX (lecture du résultat JSON)
$routes->get('poles/delete/(:num)', 'Admin\PolesController::delete/$1');
```

### POST — modifier quelque chose

- Utilisé pour **envoyer** des données (formulaires, actions)
- Les données ne sont **pas visibles** dans l'URL
- Ne peut pas être mis en favoris (protection contre les soumissions accidentelles)

```php
// Soumettre le formulaire de création d'un pôle
$routes->post('poles/store', 'Admin\PolesController::store');

// Soumettre le formulaire de mise à jour du pôle n°3
$routes->post('poles/update/(:num)', 'Admin\PolesController::update/$1');

// Approuver un paiement (action irréversible → POST obligatoire)
$routes->post('inscriptions/approuver/(:num)/(:num)', 'Admin\InscriptionsController::approuverPaiement/$1/$2');
```

### Règle simple à retenir

| Action | Méthode |
|--------|---------|
| Afficher une page / une liste | GET |
| Afficher un formulaire vide | GET |
| Soumettre un formulaire | POST |
| Action qui modifie la BDD | POST |
| Suppression AJAX (retourne JSON) | GET* |

> *Dans NEXUS, les suppressions utilisent GET car c'est une requête AJAX qui
> attend une réponse JSON — pas une soumission de formulaire classique.
> En production, on préférerait DELETE, mais GET fonctionne ici.

---

## 3. Les paramètres dans l'URL — `(:num)`

```php
$routes->get('poles/edit/(:num)', 'Admin\PolesController::edit/$1');
```

`(:num)` est un joker qui accepte **uniquement des chiffres**.
`$1` transmet ce chiffre comme argument à la méthode.

Exemples :
- `/admin/poles/edit/3` → `edit(3)` ✅
- `/admin/poles/edit/abc` → 404 ✅ (refusé car pas un nombre)

Pour les inscriptions (clé composite id_compte + id_session) :
```php
$routes->get('inscriptions/edit/(:num)/(:num)', 'Admin\InscriptionsController::edit/$1/$2');
// /admin/inscriptions/edit/5/12 → edit(5, 12)
```

---

## 4. Les groupes de routes

Au lieu de répéter `admin/` partout, on groupe les routes :

```php
$routes->group('admin', ['filter' => 'admin'], function($routes) {
    $routes->get('dashboard',        'Admin\Dashboard::index');
    $routes->get('poles',            'Admin\PolesController::index');
    $routes->get('poles/create',     'Admin\PolesController::create');
    // ...
});
```

Ce que ça fait :
- Toutes les routes à l'intérieur commencent automatiquement par `/admin/`
- Le filtre `'admin'` est appliqué **à toutes** ces routes d'un coup
- Plus lisible, plus maintenable

Dans NEXUS il y a 3 groupes :
```
admin/     → filtre 'admin'    (connecté + rôle Admin)
formateur/ → filtre 'formateur' (connecté + rôle Formateur)
etudiant/  → filtre 'etudiant'  (connecté + rôle Etudiant)
```

---

## 5. Les filtres — la sécurité des routes

Un filtre est du code qui s'exécute **avant** que la requête atteigne le contrôleur.
C'est le videur de boîte de nuit : il vérifie l'accès avant de laisser entrer.

```
Navigateur → Routes.php → FILTRE → Contrôleur → Modèle → Vue
                              ↓
                        (si refusé → redirect)
```

### AdminFilter — exemple concret

**Fichier : `app/Filters/AdminFilter.php`**

```php
public function before(RequestInterface $request, $arguments = null)
{
    // Vérification 1 : est-ce que l'utilisateur est connecté ?
    if (! session()->get('isLoggedIn')) {
        return redirect()->to('/login')
                         ->with('error', 'Vous devez être connecté.');
    }

    // Vérification 2 : est-ce qu'il est bien Admin ?
    if (session()->get('role') !== 'Admin') {
        return redirect()->to('/')
                         ->with('error', 'Accès refusé.');
    }

    // Si les deux vérifications passent → la requête continue vers le contrôleur
}
```

Double vérification :
1. **Connecté ?** → sinon redirect vers `/login`
2. **Bon rôle ?** → sinon redirect vers `/` (accueil)

Sans ce filtre, n'importe qui pourrait taper `/admin/comptes` dans son navigateur
et accéder à la liste des utilisateurs.

### Les 4 filtres du projet

| Filtre | Fichier | Vérifie |
|--------|---------|---------|
| `auth` | `AuthFilter.php` | isLoggedIn uniquement |
| `admin` | `AdminFilter.php` | isLoggedIn + role === 'Admin' |
| `formateur` | `FormateurFilter.php` | isLoggedIn + role === 'Formateur' |
| `etudiant` | `EtudiantFilter.php` | isLoggedIn + role === 'Etudiant' |

### Déclaration dans Filters.php

Avant de pouvoir utiliser un filtre dans Routes.php, il faut lui donner un alias :

```php
// app/Config/Filters.php
public array $aliases = [
    'auth'      => AuthFilter::class,
    'admin'     => AdminFilter::class,
    'formateur' => FormateurFilter::class,
    'etudiant'  => EtudiantFilter::class,
];
```

Ensuite dans Routes.php on utilise l'alias :
```php
$routes->group('admin', ['filter' => 'admin'], ...);
//                                    ↑ alias déclaré dans Filters.php
```

---

## 6. Les routes publiques — sans filtre

Certaines routes n'ont pas de filtre : elles sont accessibles par tout le monde.

```php
$routes->get('/', 'Home::index');           // Page d'accueil
$routes->get('formations', 'Home::formation'); // Liste publique des formations
$routes->get('login',  'Auth::login');      // Formulaire de connexion
$routes->post('login', 'Auth::attemptLogin'); // Traitement de la connexion
$routes->get('register',  'Auth::register'); // Formulaire d'inscription
$routes->post('register', 'Auth::attemptRegister'); // Traitement de l'inscription
```

Note : `login` a deux routes — GET pour afficher le formulaire, POST pour le traiter.

---

## 7. Le flux complet avec filtre — exemple

Un étudiant tente d'accéder à `/admin/comptes` :

```
1. GET /admin/comptes
         ↓
2. Routes.php → groupe 'admin' → filtre 'admin' déclenché
         ↓
3. AdminFilter::before() :
   - isLoggedIn = true ✅
   - role = 'Etudiant' ≠ 'Admin' ❌
         ↓
4. redirect()->to('/') avec message "Accès refusé"
         ↓
5. Le contrôleur Admin\ComptesController::index() n'est JAMAIS appelé
```

Un admin se connecte et accède à `/admin/comptes` :

```
1. GET /admin/comptes
         ↓
2. Routes.php → groupe 'admin' → filtre 'admin' déclenché
         ↓
3. AdminFilter::before() :
   - isLoggedIn = true ✅
   - role = 'Admin' ✅
         ↓
4. La requête continue vers Admin\ComptesController::index()
         ↓
5. Le contrôleur appelle le modèle, passe les données à la vue
         ↓
6. La page s'affiche
```

---

## 8. Résumé

| Concept | Rôle |
|---------|------|
| **Route GET** | Afficher une page ou des données |
| **Route POST** | Soumettre un formulaire ou déclencher une action |
| **`(:num)`** | Paramètre numérique dans l'URL (sécurisé) |
| **Groupe** | Préfixe + filtre commun pour un ensemble de routes |
| **Filtre** | Contrôle d'accès exécuté avant le contrôleur |
| **Alias** | Nom court du filtre déclaré dans `Filters.php` |
