<?php

namespace App\Models;

use CodeIgniter\Model;

class FormationModel extends Model
{
    protected $table            = 'formations';
    protected $primaryKey       = 'id_formation';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'titre',
        'description',
        'duree',
        'prix',
        'image',
        'prerequis',
        'id_pole',
    ];

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    /**
     * Récupère toutes les formations avec le nom du pôle associé.
     * Utilise un JOIN pour éviter d'afficher l'id_pole brut dans la liste.
     */
    public function getAllWithPole(): array
    {
        return $this->db->table('formations f')
            ->select('f.id_formation, f.titre, f.description, f.duree, f.prix, f.image, f.prerequis, p.nom_pole, f.id_pole')
            ->join('poles p', 'f.id_pole = p.id_pole')
            ->orderBy('f.titre', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Récupère une formation par son ID avec le nom du pôle.
     * Utilisé dans edit() pour pré-remplir le formulaire.
     *
     * @param int $id
     * @return array|null
     */
    public function getWithPole(int $id): ?array
    {
        $result = $this->db->table('formations f')
            ->select('f.*, p.nom_pole')
            ->join('poles p', 'f.id_pole = p.id_pole')
            ->where('f.id_formation', $id)
            ->get()
            ->getRowArray();

        return $result ?: null;
    }
}
