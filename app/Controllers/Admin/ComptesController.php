<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CompteModel;

class ComptesController extends BaseController
{
    private CompteModel $compteModel;

    public function __construct()
    {
        $this->compteModel = new CompteModel();
    }

    /**
     * Affiche la liste de tous les comptes avec leur rôle actuel.
     */
    public function index(): string
    {
        $data = [
            'title'   => 'Gestion des comptes',
            'comptes' => $this->compteModel->getAllWithRole(),
        ];

        return view('admin/comptes/index', $data);
    }

    /**
     * Bascule le rôle d'un compte entre Etudiant et Formateur.
     * Si le compte est Etudiant, il devient Formateur, et inversement.
     * Le rôle Admin est exclu de cette bascule.
     *
     * @param int $id_compte
     */
    public function toggleRole(int $id_compte)
    {
        $db = \Config\Database::connect();

        $current = $db->table('compte_roles cr')
            ->select('cr.id_role, r.libelle')
            ->join('roles r', 'cr.id_role = r.id_role')
            ->where('cr.id_compte', $id_compte)
            ->get()->getRowArray();

        if ($current === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Compte introuvable.");
        }

        $nouveauLibelle = $current['libelle'] === 'Etudiant' ? 'Formateur' : 'Etudiant';

        $nouveauRole = $db->table('roles')
            ->where('libelle', $nouveauLibelle)
            ->get()->getRowArray();

        $db->table('compte_roles')
            ->where('id_compte', $id_compte)
            ->update(['id_role' => $nouveauRole['id_role']]);

        session()->setFlashdata('success', 'Le rôle a été modifié avec succès.');
        return redirect()->to(site_url('admin/comptes'));
    }
}
