<?php

namespace App\Controllers\Formateur;

use App\Controllers\BaseController;
use App\Models\CompteModel;
use App\Models\InscriptionModel;
use App\Models\SessionModel;

class Dashboard extends BaseController
{
    private CompteModel      $compteModel;
    private InscriptionModel $inscriptionModel;
    private SessionModel     $sessionModel;

    public function __construct()
    {
        $this->compteModel      = new CompteModel();
        $this->inscriptionModel = new InscriptionModel();
        $this->sessionModel     = new SessionModel();
    }

    /**
     * Affiche le tableau de bord du formateur avec les sessions qui lui sont assignées.
     */
    public function index(): string
    {
        $id_formateur = (int) session()->get('id_compte');
        $sessions     = $this->sessionModel->getByFormateur($id_formateur);

        return view('formateur/dashboard', [
            'title'    => 'Dashboard',
            'sessions' => $sessions,
        ]);
    }

    /**
     * Affiche le planning du formateur : toutes ses sessions classées par date.
     */
    public function planning(): string
    {
        $id_formateur = (int) session()->get('id_compte');
        $sessions     = $this->sessionModel->getByFormateur($id_formateur);

        return view('formateur/planning', [
            'title'    => 'Mon planning',
            'sessions' => $sessions,
        ]);
    }

    /**
     * Affiche l'historique du formateur : uniquement les sessions au statut 'Terminee'.
     */
    public function historique(): string
    {
        $id_formateur = (int) session()->get('id_compte');
        $sessions     = array_filter(
            $this->sessionModel->getByFormateur($id_formateur),
            fn($s) => $s['statut'] === 'Terminee'
        );

        return view('formateur/historique', [
            'title'    => 'Mon historique',
            'sessions' => array_values($sessions),
        ]);
    }

    /**
     * Affiche le catalogue des sessions ouvertes en utilisant la vue étudiant
     * mais avec le layout et les routes du formateur.
     */
    public function catalogue(): string
    {
        $id_compte = (int) session()->get('id_compte');

        return view('etudiant/catalogue', [
            'title'      => 'Catalogue des formations',
            'sessions'   => $this->sessionModel->getSessionsOuvertes($id_compte),
            'layout'     => 'formateur/layout',
            'base_route' => 'formateur',
        ]);
    }

    /**
     * Affiche les inscriptions du formateur en utilisant la vue étudiant
     * mais avec le layout et les routes du formateur.
     */
    public function mesInscriptions(): string
    {
        $id_compte = (int) session()->get('id_compte');

        return view('etudiant/mes_inscriptions', [
            'title'        => 'Mes inscriptions',
            'inscriptions' => $this->inscriptionModel->getByCompte($id_compte),
            'layout'       => 'formateur/layout',
            'base_route'   => 'formateur',
        ]);
    }

    /**
     * Inscrit le formateur à une session.
     * Vérifie l'absence de doublon et la disponibilité des places avant d'insérer.
     *
     * @param int $id_session
     */
    public function inscrire(int $id_session)
    {
        $id_compte = (int) session()->get('id_compte');
        $session   = $this->sessionModel->find($id_session);

        if ($session['statut'] !== 'Ouverte') {
            session()->setFlashdata('error', 'Cette session n\'est plus disponible à l\'inscription.');
            return redirect()->to(site_url('formateur/catalogue'));
        }

        if ($this->inscriptionModel->isAlreadyInscrit($id_compte, $id_session)) {
            session()->setFlashdata('error', 'Vous êtes déjà inscrit à cette session.');
            return redirect()->to(site_url('formateur/catalogue'));
        }

        if ($this->inscriptionModel->countInscriptions($id_session) >= $session['place_max']) {
            session()->setFlashdata('error', 'Il n\'y a plus de places disponibles.');
            return redirect()->to(site_url('formateur/catalogue'));
        }

        $this->inscriptionModel->insert([
            'id_compte'        => $id_compte,
            'id_session'       => $id_session,
            'date_inscription' => date('Y-m-d H:i:s'),
            'etat_parcours'    => 'Inscrit',
        ]);

        session()->setFlashdata('success', 'Inscription réussie !');
        return redirect()->to(site_url('formateur/mes-inscriptions'));
    }

    /**
     * Traite l'envoi d'un paiement IBAN pour une inscription du formateur.
     * Valide le format belge (BE + 14 chiffres) et le checksum mod-97
     * avant d'enregistrer la date d'envoi.
     *
     * @param int $id_compte
     * @param int $id_session
     */
    public function envoyerPaiement(int $id_compte, int $id_session)
    {

        $this->inscriptionModel->updateByKeys($id_compte, $id_session, [
            'date_envoi_paiement' => date('Y-m-d H:i:s'),
        ]);

        session()->setFlashdata('success', 'Votre paiement a été envoyé, en attente de validation.');
        return redirect()->to(site_url('formateur/mes-inscriptions'));
    }

    /**
     * Affiche la page de profil du formateur connecté.
     */
    public function profil(): string
    {
        $id_compte = session()->get('id_compte');
        $compte    = $this->compteModel->find($id_compte);

        return view('etudiant/profil', [
            'title'      => 'Mon profil',
            'compte'     => $compte,
            'layout'     => 'formateur/layout',
            'base_route' => 'formateur',
        ]);
    }

    /**
     * Traite la modification des données du profil du formateur.
     */
    public function modifierProfil()
    {
        $id_compte = (int) session()->get('id_compte');

        $rules = [
            'nom' => [
                'rules'  => 'required|min_length[2]|max_length[50]',
                'errors' => [
                    'required'   => 'Le nom est obligatoire.',
                    'min_length' => 'Le nom doit contenir au moins 2 caractères.',
                    'max_length' => 'Le nom ne peut pas dépasser 50 caractères.',
                ],
            ],
            'prenom' => [
                'rules'  => 'required|min_length[2]|max_length[50]',
                'errors' => [
                    'required'   => 'Le prénom est obligatoire.',
                    'min_length' => 'Le prénom doit contenir au moins 2 caractères.',
                    'max_length' => 'Le prénom ne peut pas dépasser 50 caractères.',
                ],
            ],
            'email' => [
                'rules'  => "required|valid_email|max_length[100]|is_unique[comptes.email,id_compte,{$id_compte}]",
                'errors' => [
                    'required'    => 'L\'adresse email est obligatoire.',
                    'valid_email' => 'L\'adresse email n\'est pas valide.',
                    'max_length'  => 'L\'email ne peut pas dépasser 100 caractères.',
                    'is_unique'   => 'Cette adresse email est déjà utilisée par un autre compte.',
                ],
            ],
            'adresse'        => ['rules' => 'permit_empty|max_length[255]', 'errors' => []],
            'code_postal'    => ['rules' => 'permit_empty|max_length[10]',  'errors' => []],
            'date_naissance' => ['rules' => 'permit_empty|valid_date[Y-m-d]', 'errors' => ['valid_date' => 'La date de naissance n\'est pas valide.']],
        ];

        if (! $this->validate($rules)) {
            session()->setFlashdata('errors', $this->validator->getErrors());
            return redirect()->to(site_url('formateur/profil'));
        }

        $this->compteModel->update($id_compte, [
            'nom'            => $this->request->getPost('nom'),
            'prenom'         => $this->request->getPost('prenom'),
            'email'          => $this->request->getPost('email'),
            'adresse'        => $this->request->getPost('adresse'),
            'code_postal'    => $this->request->getPost('code_postal'),
            'date_naissance' => $this->request->getPost('date_naissance') ?: null,
        ]);

        session()->setFlashdata('success', 'Profil mis à jour avec succès.');
        return redirect()->to(site_url('formateur/profil'));
    }
}
