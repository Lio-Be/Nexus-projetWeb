<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    /**
     * Affiche le tableau de bord admin avec les compteurs globaux :
     * formations, pôles, sessions et nombre d'étudiants inscrits.
     */
    public function index()
    {
        $db = \Config\Database::connect();

        $nbFormations = $db->query("SELECT COUNT(*) AS total FROM formations")->getRowArray()['total'];
        $nbPoles      = $db->query("SELECT COUNT(*) AS total FROM poles")->getRowArray()['total'];
        $nbSessions   = $db->query("SELECT COUNT(*) AS total FROM sessions")->getRowArray()['total'];
        $nbEtudiants  = $db->query("
            SELECT COUNT(*) AS total FROM compte_roles cr
            JOIN roles r ON cr.id_role = r.id_role
            WHERE r.libelle = 'Etudiant'
        ")->getRowArray()['total'];

        return view('admin/dashboard', [
            'title'        => 'Dashboard',
            'nbFormations' => $nbFormations,
            'nbPoles'      => $nbPoles,
            'nbSessions'   => $nbSessions,
            'nbEtudiants'  => $nbEtudiants,
        ]);
    }
}
