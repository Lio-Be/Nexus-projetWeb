<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Contrôleur de base dont héritent tous les autres contrôleurs.
 * Permet de centraliser le chargement des composants communs (helpers, session, etc.).
 * Étendre cette classe dans chaque nouveau contrôleur :
 *     class MonController extends BaseController
 *
 * Par sécurité, déclarer toute nouvelle méthode utilitaire en protected ou private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance de l'objet Request principal (HTTP ou CLI).
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * Liste des helpers chargés automatiquement à l'instanciation.
     * Disponibles dans tous les contrôleurs enfants.
     *
     * @var list<string>
     */
    protected $helpers = [];

    /**
     * Initialise le contrôleur avec la requête, la réponse et le logger.
     * Appelé automatiquement par CodeIgniter avant chaque action.
     *
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        // Précharger ici les modèles, services ou bibliothèques communs.
        // Ex. : $this->session = service('session');
    }
}
