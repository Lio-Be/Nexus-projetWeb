<?php

namespace Config;

use CodeIgniter\Config\Filters as BaseFilters;
use CodeIgniter\Filters\Cors;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\ForceHTTPS;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\PageCache;
use CodeIgniter\Filters\PerformanceMetrics;
use CodeIgniter\Filters\SecureHeaders;
use App\Filters\ApiAuthFilter;
use App\Filters\ApiThrottleFilter;
use App\Filters\AuthFilter;
use App\Filters\AdminFilter;
use App\Filters\FormateurFilter;
use App\Filters\EtudiantFilter;

class Filters extends BaseFilters
{
    /**
     * Aliases des filtres — permet d'utiliser un nom court dans Routes.php
     * au lieu du nom complet de la classe.
     *
     * @var array<string, class-string|list<class-string>>
     *
     * [nom_du_filtre => classe]
     * ou [nom_du_filtre => [classe1, classe2, ...]]
     */
    public array $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'cors'          => Cors::class,
        'forcehttps'    => ForceHTTPS::class,
        'pagecache'     => PageCache::class,
        'performance'   => PerformanceMetrics::class,
        // Filtres du projet Nexus
        'auth'          => AuthFilter::class,
        'admin'         => AdminFilter::class,
        'formateur'     => FormateurFilter::class,
        'etudiant'      => EtudiantFilter::class,
        'api-auth'      => ApiAuthFilter::class,
        'api-throttle'  => ApiThrottleFilter::class,
    ];

    /**
     * Filtres obligatoires exécutés sur chaque requête, même si la route n'existe pas.
     * Ne pas supprimer sans savoir ce que l'on fait — ces filtres assurent des fonctions
     * essentielles du framework (cache, HTTPS, debug toolbar).
     *
     * @var array{before: list<string>, after: list<string>}
     */
    public array $required = [
        'before' => [
            'forcehttps', // Force toutes les requêtes en HTTPS
            'pagecache',  // Cache de page (performances)
        ],
        'after' => [
            'pagecache',   // Cache de page (écriture)
            'performance', // Métriques de performance
            'toolbar',     // Barre de débogage (désactivée en production)
        ],
    ];

    /**
     * Filtres appliqués globalement à toutes les requêtes (avant et après).
     * Décommenter selon les besoins de sécurité du projet.
     *
     * @var array{
     *     before: array<string, array{except: list<string>|string}>|list<string>,
     *     after: array<string, array{except: list<string>|string}>|list<string>
     * }
     */
    public array $globals = [
        'before' => [
            // 'honeypot',
            'csrf' => ['except' => ['api/*']],
            // 'invalidchars',
        ],
        'after' => [
            // 'honeypot',
            // 'secureheaders',
        ],
    ];

    /**
     * Filtres appliqués selon la méthode HTTP (GET, POST, etc.).
     *
     * Exemple :
     * 'POST' => ['csrf', 'invalidchars']
     *
     * @var array<string, list<string>>
     */
    public array $methods = [];

    /**
     * Filtres appliqués sur des patterns d'URI spécifiques (avant ou après).
     *
     * Exemple :
     * 'auth' => ['before' => ['compte/*', 'profil/*']]
     *
     * @var array<string, array<string, list<string>>>
     */
    public array $filters = [];
}
