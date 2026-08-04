<?php

namespace App\Models;

use CodeIgniter\Model;

class InscriptionModel extends Model
{
    protected $table      = 'inscriptions';
    protected $primaryKey = 'id_compte';

    protected $allowedFields = [
        'id_compte',
        'id_session',
        'date_inscription',
        'etat_parcours',
        'note_admin',
        'date_envoi_paiement',
        'date_validation_paiement',
        'raison_refus_paiement',
    ];

    public function getAllWithFormation(): array
    {
        return $this->select('inscriptions.*, comptes.nom as nom_compte, comptes.prenom as prenom_compte, comptes.email as email_compte, sessions.date_debut, sessions.date_fin, sessions.place_max, sessions.statut, formations.titre')
                    ->join('comptes', 'comptes.id_compte = inscriptions.id_compte')
                    ->join('sessions', 'sessions.id_session = inscriptions.id_session')
                    ->join('formations', 'formations.id_formation = sessions.id_formation')
                    ->orderBy('inscriptions.date_inscription', 'DESC')
                    ->findAll();
    }

    public function getByCompte(int $id_compte): array
    {
        return $this->select('inscriptions.*, sessions.date_debut, sessions.date_fin, sessions.statut, formations.titre, formations.prix')
                    ->join('sessions', 'sessions.id_session = inscriptions.id_session')
                    ->join('formations', 'formations.id_formation = sessions.id_formation')
                    ->where('inscriptions.id_compte', $id_compte)
                    ->orderBy('inscriptions.date_inscription', 'DESC')
                    ->findAll();
    }

    public function getWithDetails(int $id_compte, int $id_session): ?array
    {
        return $this->select('inscriptions.*, comptes.nom as nom_compte, comptes.prenom as prenom_compte, comptes.email as email_compte, sessions.date_debut, sessions.date_fin, sessions.place_max, sessions.statut, formations.titre')
                    ->join('comptes', 'comptes.id_compte = inscriptions.id_compte')
                    ->join('sessions', 'sessions.id_session = inscriptions.id_session')
                    ->join('formations', 'formations.id_formation = sessions.id_formation')
                    ->where('inscriptions.id_compte', $id_compte)
                    ->where('inscriptions.id_session', $id_session)
                    ->first();
    }

    public function isAlreadyInscrit(int $id_compte, int $id_session): bool
    {
        return $this->where('id_compte', $id_compte)
                    ->where('id_session', $id_session)
                    ->countAllResults() > 0;
    }

    public function countInscriptions(int $id_session): int
    {
        return $this->where('id_session', $id_session)
                    ->countAllResults();
    }

    public function updateByKeys(int $id_compte, int $id_session, array $data): void
    {
        $this->db->table($this->table)
                 ->where('id_compte', $id_compte)
                 ->where('id_session', $id_session)
                 ->update($data);
    }

    public function deleteByKeys(int $id_compte, int $id_session): void
    {
        $this->db->table($this->table)
                 ->where('id_compte', $id_compte)
                 ->where('id_session', $id_session)
                 ->delete();
    }
}
