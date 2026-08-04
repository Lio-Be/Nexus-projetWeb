<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('home/index', ['title' => 'Accueil']);
    }

    public function formation()
    {
        $db = \Config\Database::connect();

        $resultats = $db->query("
            SELECT p.nom_pole, f.titre, f.description, f.duree, f.prix
            FROM formations f
            JOIN poles p ON f.id_pole = p.id_pole
            ORDER BY p.nom_pole, f.titre
        ")->getResultArray();

        return view('home/formation', [
            'title'     => 'Formations',
            'resultats' => $resultats,
        ]);
    }
}
