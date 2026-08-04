<?php

namespace App\Models;

use CodeIgniter\Model;

class PoleModel extends Model
{
    protected $table      = 'poles';
    protected $primaryKey = 'id_pole';

    protected $allowedFields = ['nom_pole', 'description'];

    protected $validationRules = [];

    protected $validationMessages = [
        'nom_pole' => [
            'required'   => 'Le nom du pôle est obligatoire',
            'min_length' => 'Le nom doit contenir au moins 2 caractères',
            'max_length' => 'Le nom ne peut pas dépasser 100 caractères',
            'is_unique'  => 'Ce nom de pôle existe déjà',
        ],
        'description' => [
            'max_length' => 'La description ne peut pas dépasser 1000 caractères',
        ],
    ];

    protected $skipValidation = false;
}
