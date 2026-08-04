<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'comptes';
    protected $primaryKey = 'id_compte';

    protected $allowedFields = [
        'nom',
        'prenom',
        'email',
        'password',
        'adresse',
        'code_postal',
        'date_naissance',
        'photo_profil',
    ];

    /**
     * Recherche un compte par email et retourne aussi son rôle.
     * En cas de rôles multiples, la priorité est : Admin > Formateur > Etudiant.
     *
     * @param string $email
     * @return array|null
     */
    public function findByEmail($email)
    {
        $builder = $this->db->table('comptes c');
        $builder->select('c.*, r.libelle as role, r.id_role');
        $builder->join('compte_roles cr', 'c.id_compte = cr.id_compte', 'left');
        $builder->join('roles r', 'cr.id_role = r.id_role', 'left');
        $builder->where('c.email', $email);
        $builder->orderBy("FIELD(r.libelle, 'Admin', 'Formateur', 'Etudiant')", '', false);
        $builder->limit(1);

        return $builder->get()->getRowArray();
    }
}
