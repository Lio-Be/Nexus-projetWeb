<?php

namespace App\Filters;

use App\Models\ApiTokenModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ApiAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $authHeader = $request->getHeaderLine('Authorization');

        if (! preg_match('/^Bearer\s+(\S+)$/', $authHeader, $matches)) {
            return $this->unauthorized('Token manquant ou format invalide (attendu : Bearer <token>).');
        }

        $tokenRow = (new ApiTokenModel())->findValidToken($matches[1]);

        if ($tokenRow === null) {
            return $this->unauthorized('Token invalide ou expiré.');
        }

        // Transmet l'id_compte au contrôleur via le superglobal server de la requête.
        // Accessible ensuite avec $this->request->getServer('API_COMPTE_ID').
        $server = (array) $request->getServer();
        $server['API_COMPTE_ID'] = $tokenRow['id_compte'];
        $request->setGlobal('server', $server);

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }

    private function unauthorized(string $message): ResponseInterface
    {
        return service('response')
            ->setStatusCode(401)
            ->setContentType('application/json')
            ->setBody(json_encode(['status' => 401, 'message' => $message]));
    }
}
