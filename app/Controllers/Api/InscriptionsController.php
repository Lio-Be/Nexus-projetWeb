<?php

namespace App\Controllers\Api;

use App\Models\InscriptionModel;
use CodeIgniter\RESTful\ResourceController;

/**
 * Endpoints protégés par le filtre api-auth : le compte est identifié par son
 * token, jamais par un paramètre de la requête.
 */
class InscriptionsController extends ResourceController
{
    protected $modelName = InscriptionModel::class;
    protected $format    = 'json';

    public function mesInscriptions()
    {
        // Renseigné par ApiAuthFilter après validation du token.
        $idCompte = (int) $this->request->getServer('API_COMPTE_ID');

        return $this->respond([
            'status' => 200,
            'data'   => $this->model->getByCompte($idCompte),
        ]);
    }
}
