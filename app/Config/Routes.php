<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

/* ── Pages publiques (sans filtre) ── */
$routes->get('/', 'Home::index');
$routes->get('formations', 'Home::formation');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attemptLogin');
$routes->get('logout', 'Auth::logout');
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::attemptRegister');

/* ── Zone Admin (filtre : connecté + rôle Admin) ── */
$routes->group('admin', ['filter' => 'admin'], function($routes) {

    $routes->get('dashboard', 'Admin\Dashboard::index');

    /* Pôles */
    $routes->get('poles', 'Admin\PolesController::index');
    $routes->get('poles/create', 'Admin\PolesController::create');
    $routes->post('poles/store', 'Admin\PolesController::store');
    $routes->get('poles/edit/(:num)', 'Admin\PolesController::edit/$1');
    $routes->post('poles/update/(:num)', 'Admin\PolesController::update/$1');
    $routes->get('poles/delete/(:num)', 'Admin\PolesController::delete/$1');

    /* Formations */
    $routes->get('formations', 'Admin\FormationsController::index');
    $routes->get('formations/create', 'Admin\FormationsController::create');
    $routes->post('formations/store', 'Admin\FormationsController::store');
    $routes->get('formations/edit/(:num)', 'Admin\FormationsController::edit/$1');
    $routes->post('formations/update/(:num)', 'Admin\FormationsController::update/$1');
    $routes->get('formations/delete/(:num)', 'Admin\FormationsController::delete/$1');

    /* Sessions */
    $routes->get('sessions', 'Admin\SessionsController::index');
    $routes->get('sessions/create', 'Admin\SessionsController::create');
    $routes->post('sessions/store', 'Admin\SessionsController::store');
    $routes->get('sessions/edit/(:num)', 'Admin\SessionsController::edit/$1');
    $routes->post('sessions/update/(:num)', 'Admin\SessionsController::update/$1');
    $routes->get('sessions/delete/(:num)', 'Admin\SessionsController::delete/$1');

    /* Inscriptions */
    $routes->get('inscriptions', 'Admin\InscriptionsController::index');
    $routes->get('inscriptions/create', 'Admin\InscriptionsController::create');
    $routes->post('inscriptions/store', 'Admin\InscriptionsController::store');
    $routes->get('inscriptions/delete/(:num)/(:num)', 'Admin\InscriptionsController::delete/$1/$2');
    $routes->get('inscriptions/edit/(:num)/(:num)', 'Admin\InscriptionsController::edit/$1/$2');
    $routes->post('inscriptions/update/(:num)/(:num)', 'Admin\InscriptionsController::update/$1/$2');
    $routes->post('inscriptions/approuver/(:num)/(:num)', 'Admin\InscriptionsController::approuverPaiement/$1/$2');
    $routes->post('inscriptions/refuser/(:num)/(:num)', 'Admin\InscriptionsController::refuserPaiement/$1/$2');

    /* Comptes */
    $routes->get('comptes', 'Admin\ComptesController::index');
    $routes->post('comptes/toggle-role/(:num)', 'Admin\ComptesController::toggleRole/$1');

});

/* ── Zone Formateur (filtre : connecté + rôle Formateur) ── */
$routes->group('formateur', ['filter' => 'formateur'], function($routes) {

    $routes->get('dashboard',      'Formateur\Dashboard::index');
    $routes->get('planning',       'Formateur\Dashboard::planning');
    $routes->get('historique',     'Formateur\Dashboard::historique');

    /* catalogue et inscriptions */
    $routes->get('catalogue',                              'Formateur\Dashboard::catalogue');
    $routes->post('inscrire/(:num)',                       'Formateur\Dashboard::inscrire/$1');
    $routes->get('profil', 'Formateur\Dashboard::profil');
    $routes->post('profil/modifier', 'Formateur\Dashboard::modifierProfil');
    $routes->get('mes-inscriptions',                       'Formateur\Dashboard::mesInscriptions');
    $routes->post('paiement/envoyer/(:num)/(:num)',        'Formateur\Dashboard::envoyerPaiement/$1/$2');

});

/* ── Zone Etudiant (filtre : connecté + rôle Etudiant) ── */
$routes->group('etudiant', ['filter' => 'etudiant'], function($routes) {
    $routes->get('dashboard', 'Etudiant\Dashboard::index');
    $routes->get('mes-inscriptions', 'Etudiant\Dashboard::mesInscriptions');
    $routes->post('paiement/envoyer/(:num)/(:num)', 'Etudiant\Dashboard::envoyerPaiement/$1/$2');
    $routes->get('catalogue', 'Etudiant\Dashboard::catalogue');
    $routes->post('inscrire/(:num)', 'Etudiant\Dashboard::inscrire/$1');
    $routes->get('profil', 'Etudiant\Dashboard::profil');
    $routes->post('profil/modifier', 'Etudiant\Dashboard::modifierProfil');
});

/* ── API REST ── */
$routes->group('api', ['namespace' => 'App\Controllers\Api'], function($routes) {
    // Public — pas d'authentification requise
    $routes->get('formations', 'FormationsController::index');
    $routes->post('login', 'AuthController::login', ['filter' => 'api-throttle']);

    // Protégé — token requis (en-tête Authorization: Bearer <token>)
    $routes->group('', ['filter' => 'api-auth'], static function ($routes) {
        $routes->get('mes-inscriptions', 'InscriptionsController::mesInscriptions');
    });
});
