<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Limite le nombre de requêtes par adresse IP (protection contre le brute force
 * sur /api/login). Utilise le Throttler de CodeIgniter (algorithme du seau à jetons).
 */
class ApiThrottleFilter implements FilterInterface
{
    public const MAX_TENTATIVES = 5;

    public function before(RequestInterface $request, $arguments = null)
    {
        $cle = 'api-login-' . md5($request->getIPAddress());

        if (service('throttler')->check($cle, self::MAX_TENTATIVES, MINUTE) === false) {
            return service('response')
                ->setStatusCode(429)
                ->setHeader('Retry-After', (string) service('throttler')->getTokenTime())
                ->setContentType('application/json')
                ->setBody(json_encode([
                    'status'  => 429,
                    'message' => 'Trop de tentatives. Réessayez plus tard.',
                ]));
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
