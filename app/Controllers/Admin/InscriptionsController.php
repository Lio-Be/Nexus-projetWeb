<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\InscriptionModel;
use App\Models\SessionModel;
use App\Models\CompteModel;

class InscriptionsController extends BaseController
{
    private InscriptionModel $inscriptionModel;
    private SessionModel     $sessionModel;
    private CompteModel      $compteModel;

    public function __construct()
    {
        $this->inscriptionModel = new InscriptionModel();
        $this->sessionModel     = new SessionModel();
        $this->compteModel      = new CompteModel();
    }

    /**
     * Affiche la liste de toutes les inscriptions avec les infos du compte, session et formation.
     */
    public function index(): string
    {
        $data = [
            'title'        => 'Gestion des Inscriptions',
            'inscriptions' => $this->inscriptionModel->getAllWithFormation(),
        ];

        return view('admin/inscriptions/index', $data);
    }

    /**
     * Affiche le formulaire de création d'une inscription manuelle.
     * Charge la liste des comptes et des sessions disponibles pour les <select>.
     */
    public function create(): string
    {
        $data = [
            'title'    => 'Nouvelle Inscription',
            'comptes'  => $this->getComptes(),
            'sessions' => $this->sessionModel->getAllWithFormation(),
            'errors'   => session()->getFlashdata('errors') ?? [],
            'old'      => session()->getFlashdata('old')    ?? [],
        ];

        return view('admin/inscriptions/create', $data);
    }

    /**
     * Traite le formulaire de création d'une inscription.
     * Vérifie que la session est ouverte, qu'il reste des places et que le compte
     * n'est pas déjà inscrit avant d'insérer.
     */
    public function store()
    {
        $rules = [
            'id_compte' => [
                'rules'  => 'required|integer',
                'errors' => ['required' => 'Veuillez sélectionner un compte.'],
            ],
            'id_session' => [
                'rules'  => 'required|integer',
                'errors' => ['required' => 'Veuillez sélectionner une session.'],
            ],
        ];

        if (! $this->validate($rules)) {
            session()->setFlashdata('errors', $this->validator->getErrors());
            session()->setFlashdata('old', $this->request->getPost());
            return redirect()->to(site_url('admin/inscriptions/create'));
        }

        $id_compte  = (int) $this->request->getPost('id_compte');
        $id_session = (int) $this->request->getPost('id_session');
        $session    = $this->sessionModel->find($id_session);

        if ($this->inscriptionModel->isAlreadyInscrit($id_compte, $id_session)) {
            session()->setFlashdata('error', 'Ce compte est déjà inscrit à cette session.');
            return redirect()->to(site_url('admin/inscriptions/create'));
        }

        if ($session['statut'] !== 'Ouverte') {
            session()->setFlashdata('error', 'Cette session n\'est pas ouverte aux inscriptions.');
            return redirect()->to(site_url('admin/inscriptions/create'));
        }

        if ($this->inscriptionModel->countInscriptions($id_session) >= $session['place_max']) {
            session()->setFlashdata('error', 'Il n\'y a plus de places disponibles.');
            return redirect()->to(site_url('admin/inscriptions/create'));
        }

        $data = [
            'id_compte'        => $id_compte,
            'id_session'       => $id_session,
            'date_inscription' => date('Y-m-d H:i:s'),
            'etat_parcours'    => 'Inscrit',
        ];

        $this->inscriptionModel->insert($data);

        session()->setFlashdata('success', 'L\'inscription a été créée avec succès.');
        return redirect()->to(site_url('admin/inscriptions'));
    }

    /**
     * Affiche le formulaire de modification d'une inscription identifiée par la clé composite.
     * Lève une 404 si l'inscription n'existe pas.
     *
     * @param int $id_compte
     * @param int $id_session
     */
    public function edit(int $id_compte, int $id_session): string
    {
        $inscription = $this->inscriptionModel->getWithDetails($id_compte, $id_session);

        if ($inscription === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Inscription introuvable.");
        }

        $data = [
            'title'       => 'Modifier l\'inscription',
            'inscription' => $inscription,
            'errors'      => session()->getFlashdata('errors') ?? [],
            'old'         => session()->getFlashdata('old')    ?? [],
        ];

        return view('admin/inscriptions/edit', $data);
    }

    /**
     * Traite le formulaire de modification : met à jour l'état de parcours et la note admin.
     *
     * @param int $id_compte
     * @param int $id_session
     */
    public function update(int $id_compte, int $id_session)
    {
        $rules = [
            'etat_parcours' => [
                'rules'  => 'required|in_list[Inscrit,En_cours,Termine,Abandonne]',
                'errors' => ['required' => 'Veuillez sélectionner un état.'],
            ],
            'note_admin' => [
                'rules'  => 'permit_empty|max_length[500]',
                'errors' => ['max_length' => 'La note ne peut pas dépasser 500 caractères.'],
            ],
        ];

        if (! $this->validate($rules)) {
            session()->setFlashdata('errors', $this->validator->getErrors());
            session()->setFlashdata('old', $this->request->getPost());
            return redirect()->to(site_url("admin/inscriptions/edit/$id_compte/$id_session"));
        }

        $data = [
            'etat_parcours' => $this->request->getPost('etat_parcours'),
            'note_admin'    => $this->request->getPost('note_admin') ?: null,
        ];

        $this->inscriptionModel->updateByKeys($id_compte, $id_session, $data);

        session()->setFlashdata('success', 'L\'inscription a été modifiée avec succès.');
        return redirect()->to(site_url('admin/inscriptions'));
    }

    /**
     * Supprime une inscription identifiée par la clé composite.
     * Supporte les requêtes AJAX (retourne du JSON) et les requêtes classiques.
     *
     * @param int $id_compte
     * @param int $id_session
     */
    public function delete(int $id_compte, int $id_session)
    {
        $inscription = $this->inscriptionModel->getWithDetails($id_compte, $id_session);

        if ($inscription === null) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Inscription introuvable.']);
            }
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Inscription introuvable.");
        }

        $this->inscriptionModel->deleteByKeys($id_compte, $id_session);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true, 'message' => 'L\'inscription a été supprimée.']);
        }

        session()->setFlashdata('success', 'L\'inscription a été supprimée.');
        return redirect()->to(site_url('admin/inscriptions'));
    }

    /**
     * Valide le paiement d'une inscription en enregistrant la date de validation.
     * Redirige vers la page de modification de l'inscription concernée.
     *
     * @param int $id_compte
     * @param int $id_session
     */
    public function approuverPaiement(int $id_compte, int $id_session)
    {
        $this->inscriptionModel->updateByKeys($id_compte, $id_session, [
            'date_validation_paiement' => date('Y-m-d H:i:s'),
        ]);

        session()->setFlashdata('success', 'Le paiement a été approuvé avec succès.');
        return redirect()->to(site_url("admin/inscriptions/edit/$id_compte/$id_session"));
    }

    /**
     * Refuse le paiement d'une inscription : enregistre la raison du refus et
     * réinitialise la date d'envoi pour permettre un nouvel envoi.
     *
     * @param int $id_compte
     * @param int $id_session
     */
    public function refuserPaiement(int $id_compte, int $id_session)
    {
        $rules = [
            'raison_refus_paiement' => [
                'rules'  => 'required|max_length[500]',
                'errors' => [
                    'required'   => 'La raison du refus est obligatoire.',
                    'max_length' => 'La raison ne peut pas dépasser 500 caractères.',
                ],
            ],
        ];

        if (! $this->validate($rules)) {
            session()->setFlashdata('errors', $this->validator->getErrors());
            return redirect()->to(site_url("admin/inscriptions/edit/$id_compte/$id_session"));
        }

        $this->inscriptionModel->updateByKeys($id_compte, $id_session, [
            'raison_refus_paiement' => $this->request->getPost('raison_refus_paiement'),
            'date_envoi_paiement'   => null,
        ]);

        session()->setFlashdata('error', 'Le paiement a été refusé avec succès.');
        return redirect()->to(site_url("admin/inscriptions/edit/$id_compte/$id_session"));
    }

    /**
     * Retourne la liste de tous les comptes.
     * Utilisée pour alimenter le <select> dans le formulaire de création.
     */
    private function getComptes(): array
    {
        return $this->compteModel->findAll();
    }
}
