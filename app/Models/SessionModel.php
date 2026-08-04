<?php

namespace App\Models;

use CodeIgniter\Model;

class SessionModel extends Model
{
    protected $table      = 'sessions';
    protected $primaryKey = 'id_session';

    protected $allowedFields = [
        'id_formation',
        'id_formateur',
        'date_debut',
        'date_fin',
        'modalite',
        'statut',
        'place_max',
        'lieu',
        'lien_visio',
    ];

    public function getAllWithFormation(): array
    {
        return $this->select('sessions.*, formations.titre AS titre_formation, poles.nom_pole, comptes.nom AS nom_formateur, comptes.prenom AS prenom_formateur')
                    ->join('formations', 'formations.id_formation = sessions.id_formation')
                    ->join('poles', 'poles.id_pole = formations.id_pole')
                    ->join('comptes', 'comptes.id_compte = sessions.id_formateur', 'left')
                    ->orderBy('sessions.date_debut', 'DESC')
                    ->findAll();
    }

    public function getWithFormation(int $id): ?array
    {
        return $this->select('sessions.*, formations.titre AS titre_formation')
                    ->join('formations', 'formations.id_formation = sessions.id_formation')
                    ->where('sessions.id_session', $id)
                    ->first();
    }

    public function getSessionsOuvertes(int $id_compte): array
    {
        $sql = "SELECT sessions.*, formations.titre AS titre_formation,
                (SELECT COUNT(*) FROM inscriptions WHERE id_session = sessions.id_session) AS places_prises,
                (SELECT COUNT(*) FROM inscriptions WHERE id_session = sessions.id_session AND id_compte = ?) AS deja_inscrit
                FROM sessions
                JOIN formations ON formations.id_formation = sessions.id_formation
                WHERE sessions.statut = 'Ouverte'
                ORDER BY sessions.date_debut ASC";

        return $this->db->query($sql, [$id_compte])->getResultArray();
    }

    public function getByFormateur(int $id_formateur): array
    {
        return $this->select('sessions.*, formations.titre AS titre_formation,
                (SELECT COUNT(*) FROM inscriptions WHERE id_session = sessions.id_session) AS places_prises', false)
                    ->join('formations', 'formations.id_formation = sessions.id_formation')
                    ->where('sessions.id_formateur', $id_formateur)
                    ->orderBy('sessions.date_debut', 'ASC')
                    ->findAll();
    }
}
