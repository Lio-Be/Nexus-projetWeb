<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Vérifie que l'utilisateur est connecté ET que son rôle est Admin.
 * Utilisé sur toutes les routes /admin/...
 */
class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }

        if (session()->get('role') !== 'Admin') {
            return redirect()->to('/')->with('error', 'Accès refusé : vous n\'avez pas les droits administrateur.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
