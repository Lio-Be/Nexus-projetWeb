<?php

namespace App\Models;

use CodeIgniter\Model;

class CompteModel extends Model
{
    protected $table            = 'comptes';
    protected $primaryKey       = 'id_compte';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'nom',
        'prenom',
        'email',
        'adresse',
        'code_postal',
        'date_naissance',
        'photo_profil',
    ];

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function getAllWithRole(): array
    {
        return $this->select('comptes.id_compte, comptes.nom, comptes.prenom, comptes.email, roles.libelle AS role, roles.id_role')
                    ->join('compte_roles', 'compte_roles.id_compte = comptes.id_compte', 'left')
                    ->join('roles', 'roles.id_role = compte_roles.id_role', 'left')
                    ->orderBy('comptes.nom', 'ASC')
                    ->findAll();
    }
}
