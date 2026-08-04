<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SessionModel;
use App\Models\FormationModel;

class SessionsController extends BaseController
{
    private SessionModel   $sessionModel;
    private FormationModel $formationModel;

    public function __construct()
    {
        $this->sessionModel   = new SessionModel();
        $this->formationModel = new FormationModel();
    }

    /**
     * Affiche la liste de toutes les sessions avec leur formation, pôle et formateur.
     */
    public function index(): string
    {
        $data = [
            'title'    => 'Gestion des Sessions',
            'sessions' => $this->sessionModel->getAllWithFormation(),
        ];

        return view('admin/sessions/index', $data);
    }

    /**
     * Affiche le formulaire de création d'une session.
     * Charge les formations et la liste des formateurs pour les <select>.
     */
    public function create(): string
    {
        $data = [
            'title'      => 'Nouvelle Session',
            'formations' => $this->formationModel->findAll(),
            'formateurs' => $this->getFormateurs(),
            'errors'     => session()->getFlashdata('errors') ?? [],
            'old'        => session()->getFlashdata('old')    ?? [],
        ];

        return view('admin/sessions/create', $data);
    }

    /**
     * Traite le formulaire de création : valide les données et insère la session.
     * Le statut est fixé à 'A_venir' à la création.
     */
    public function store()
    {
        $rules = [
            'id_formation' => [
                'rules'  => 'required|integer',
                'errors' => ['required' => 'Veuillez sélectionner une formation.'],
            ],
            'id_formateur' => [
                'rules'  => 'required|integer',
                'errors' => ['required' => 'Veuillez sélectionner un formateur.'],
            ],
            'date_debut' => [
                'rules'  => 'required|valid_date[Y-m-d]',
                'errors' => ['required' => 'La date de début est obligatoire.'],
            ],
            'date_fin' => [
                'rules'  => 'required|valid_date[Y-m-d]',
                'errors' => ['required' => 'La date de fin est obligatoire.'],
            ],
            'modalite' => [
                'rules'  => 'required|in_list[En_ligne,Presentiel]',
                'errors' => ['required' => 'Veuillez choisir le mode de la session.'],
            ],
            'place_max' => [
                'rules'  => 'required|integer|greater_than[0]',
                'errors' => [
                    'required'     => 'Le nombre de places est obligatoire.',
                    'integer'      => 'Le nombre de places doit être un entier.',
                    'greater_than' => 'Le nombre de places doit être supérieur à 0.',
                ],
            ],
            'lieu'       => ['rules' => 'permit_empty|max_length[255]', 'errors' => []],
            'lien_visio' => ['rules' => 'permit_empty|max_length[255]', 'errors' => []],
        ];

        if (! $this->validate($rules)) {
            session()->setFlashdata('errors', $this->validator->getErrors());
            session()->setFlashdata('old', $this->request->getPost());
            return redirect()->to(site_url('admin/sessions/create'));
        }

        $data = [
            'id_formation' => $this->request->getPost('id_formation'),
            'id_formateur' => $this->request->getPost('id_formateur'),
            'date_debut'   => $this->request->getPost('date_debut'),
            'date_fin'     => $this->request->getPost('date_fin'),
            'modalite'     => $this->request->getPost('modalite'),
            'statut'       => 'A_venir',
            'place_max'    => $this->request->getPost('place_max'),
            'lieu'         => $this->request->getPost('lieu') ?: null,
            'lien_visio'   => $this->request->getPost('lien_visio') ?: null,
        ];

        $this->sessionModel->insert($data);

        session()->setFlashdata('success', 'La session a été créée avec succès.');
        return redirect()->to(site_url('admin/sessions'));
    }

    /**
     * Affiche le formulaire de modification d'une session existante.
     * Lève une 404 si la session n'existe pas.
     *
     * @param int $id
     */
    public function edit(int $id): string
    {
        $session = $this->sessionModel->find($id);

        if ($session === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Session #$id introuvable.");
        }

        $data = [
            'title'      => 'Modifier la session',
            'session'    => $session,
            'formations' => $this->formationModel->findAll(),
            'formateurs' => $this->getFormateurs(),
            'errors'     => session()->getFlashdata('errors') ?? [],
            'old'        => session()->getFlashdata('old')    ?? [],
        ];

        return view('admin/sessions/edit', $data);
    }

    /**
     * Traite le formulaire de modification d'une session.
     * Permet aussi de changer manuellement le statut (A_venir, Ouverte, En_cours, Terminee, Annulee).
     *
     * @param int $id
     */
    public function update(int $id)
    {
        $session = $this->sessionModel->find($id);

        if ($session === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Session #$id introuvable.");
        }

        $rules = [
            'id_formation' => ['rules' => 'required|integer', 'errors' => []],
            'id_formateur' => ['rules' => 'required|integer', 'errors' => ['required' => 'Veuillez sélectionner un formateur.']],
            'date_debut'   => ['rules' => 'required|valid_date[Y-m-d]', 'errors' => []],
            'date_fin'     => ['rules' => 'required|valid_date[Y-m-d]', 'errors' => []],
            'modalite'     => ['rules' => 'required|in_list[En_ligne,Presentiel]', 'errors' => []],
            'statut'       => ['rules' => 'required|in_list[A_venir,Ouverte,En_cours,Terminee,Annulee]', 'errors' => []],
            'place_max'    => ['rules' => 'required|integer|greater_than[0]', 'errors' => []],
            'lieu'         => ['rules' => 'permit_empty|max_length[255]', 'errors' => []],
            'lien_visio'   => ['rules' => 'permit_empty|max_length[255]', 'errors' => []],
        ];

        if (! $this->validate($rules)) {
            session()->setFlashdata('errors', $this->validator->getErrors());
            session()->setFlashdata('old', $this->request->getPost());
            return redirect()->to(site_url("admin/sessions/edit/$id"));
        }

        $data = [
            'id_formation' => $this->request->getPost('id_formation'),
            'id_formateur' => $this->request->getPost('id_formateur'),
            'date_debut'   => $this->request->getPost('date_debut'),
            'date_fin'     => $this->request->getPost('date_fin'),
            'modalite'     => $this->request->getPost('modalite'),
            'statut'       => $this->request->getPost('statut'),
            'place_max'    => $this->request->getPost('place_max'),
            'lieu'         => $this->request->getPost('lieu') ?: null,
            'lien_visio'   => $this->request->getPost('lien_visio') ?: null,
        ];

        $this->sessionModel->update($id, $data);

        session()->setFlashdata('success', 'La session a été modifiée avec succès.');
        return redirect()->to(site_url('admin/sessions'));
    }

    /**
     * Supprime une session.
     * Supporte les requêtes AJAX (retourne du JSON) et les requêtes classiques.
     *
     * @param int $id
     */
    public function delete(int $id)
    {
        $session = $this->sessionModel->find($id);

        if ($session === null) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Session introuvable.']);
            }
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Session #$id introuvable.");
        }

        $this->sessionModel->delete($id);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true, 'message' => 'La session a été supprimée.']);
        }

        session()->setFlashdata('success', 'La session a été supprimée.');
        return redirect()->to(site_url('admin/sessions'));
    }

    /**
     * Retourne la liste des comptes ayant le rôle Formateur.
     * Utilisée pour alimenter le <select> formateur dans les formulaires de session.
     */
    private function getFormateurs(): array
    {
        $db = \Config\Database::connect();

        return $db->query("
            SELECT c.id_compte, c.nom, c.prenom
            FROM comptes c
            JOIN compte_roles cr ON c.id_compte = cr.id_compte
            JOIN roles r         ON cr.id_role = r.id_role
            WHERE r.libelle = 'Formateur'
            ORDER BY c.nom, c.prenom
        ")->getResultArray();
    }
}
