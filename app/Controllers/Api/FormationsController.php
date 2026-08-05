<?php

namespace App\Controllers\Api;

use App\Models\FormationModel;
use CodeIgniter\RESTful\ResourceController;

class FormationsController extends ResourceController
{
    protected $modelName = FormationModel::class;
    protected $format    = 'json';

    public function index()
    {
        return $this->respond([
            'status' => 200,
            'data'   => $this->model->getAllWithPole(),
        ]);
    }
}
