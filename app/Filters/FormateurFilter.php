<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Vérifie que l'utilisateur est connecté ET que son rôle est Formateur.
 * Utilisé sur toutes les routes /formateur/...
 */
class FormateurFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }

        if (session()->get('role') !== 'Formateur') {
            return redirect()->to('/')->with('error', 'Accès refusé : cette section est réservée aux formateurs.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
