<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PoleModel;

class PolesController extends BaseController
{
    protected $poleModel;

    public function __construct()
    {
        $this->poleModel = new PoleModel();
    }

    /**
     * Affiche la liste de tous les pôles.
     */
    public function index()
    {
        $data = [
            'title' => 'Gestion des Pôles',
            'poles' => $this->poleModel->findAll(),
        ];

        return view('admin/poles/index', $data);
    }

    /**
     * Affiche le formulaire de création d'un pôle.
     * Restitue les anciennes valeurs et erreurs après un échec de validation.
     */
    public function create()
    {
        $data = [
            'title'  => 'Nouveau pôle',
            'errors' => session()->getFlashdata('errors') ?? [],
            'old'    => session()->getFlashdata('old')    ?? [],
        ];

        return view('admin/poles/create', $data);
    }

    /**
     * Traite le formulaire de création : valide les données et insère le pôle.
     * Redirige vers la liste en cas de succès, sinon renvoie vers le formulaire.
     */
    public function store()
    {
        $this->poleModel->setValidationRules([
            'nom_pole'    => 'required|min_length[2]|max_length[100]|is_unique[poles.nom_pole]',
            'description' => 'permit_empty|max_length[1000]',
        ]);

        $data = [
            'nom_pole'    => $this->request->getPost('nom_pole'),
            'description' => $this->request->getPost('description'),
        ];

        if ($this->poleModel->insert($data)) {
            return redirect()->to('/admin/poles')
                ->with('success', 'Pôle créé avec succès !');
        }

        return redirect()->back()
            ->withInput()
            ->with('errors', $this->poleModel->errors());
    }

    /**
     * Affiche le formulaire de modification d'un pôle existant.
     * Retourne une erreur si le pôle n'existe pas.
     *
     * @param int $id
     */
    public function edit($id)
    {
        $pole = $this->poleModel->find($id);

        if (!$pole) {
            return redirect()->to('/admin/poles')
                ->with('error', 'Pôle introuvable');
        }

        return view('admin/poles/edit', [
            'title'  => 'Modifier : ' . $pole['nom_pole'],
            'pole'   => $pole,
            'errors' => session()->getFlashdata('errors') ?? [],
            'old'    => session()->getFlashdata('old')    ?? [],
        ]);
    }

    /**
     * Traite le formulaire de modification d'un pôle.
     * La règle is_unique ignore l'enregistrement courant pour permettre de garder le même nom.
     *
     * @param int $id
     */
    public function update($id)
    {
        $pole = $this->poleModel->find($id);

        if (!$pole) {
            return redirect()->to('/admin/poles')
                ->with('error', 'Pôle introuvable');
        }

        $this->poleModel->setValidationRules([
            'nom_pole'    => "required|min_length[2]|max_length[100]|is_unique[poles.nom_pole,id_pole,{$id}]",
            'description' => 'permit_empty|max_length[1000]',
        ]);

        $data = [
            'nom_pole'    => $this->request->getPost('nom_pole'),
            'description' => $this->request->getPost('description'),
        ];

        if ($this->poleModel->update($id, $data)) {
            return redirect()->to('/admin/poles')
                ->with('success', 'Pôle modifié avec succès !');
        }

        return redirect()->back()
            ->withInput()
            ->with('errors', $this->poleModel->errors());
    }

    /**
     * Supprime un pôle après vérification qu'aucune formation ne l'utilise.
     * Supporte les requêtes AJAX (retourne du JSON) et les requêtes classiques.
     *
     * @param int $id
     */
    public function delete($id)
    {
        $pole = $this->poleModel->find($id);

        if ($pole === null) {
            if ($this->request->isAJAX()) {
                return $this->response
                    ->setContentType('application/json')
                    ->setBody(json_encode(['success' => false, 'message' => "Pôle #$id introuvable."]));
            }
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Pôle #$id introuvable.");
        }

        $db = \Config\Database::connect();
        $nbFormations = $db->table('formations')
            ->where('id_pole', $id)
            ->countAllResults();

        if ($nbFormations > 0) {
            if ($this->request->isAJAX()) {
                return $this->response
                    ->setContentType('application/json')
                    ->setBody(json_encode([
                        'success' => false,
                        'message' => "Impossible : {$nbFormations} formation(s) utilisent ce pôle.",
                    ]));
            }
            session()->setFlashdata('error', "Impossible : {$nbFormations} formation(s) utilisent ce pôle.");
            return redirect()->to(site_url('admin/poles'));
        }

        $this->poleModel->delete($id);

        if ($this->request->isAJAX()) {
            return $this->response
                ->setContentType('application/json')
                ->setBody(json_encode([
                    'success' => true,
                    'message' => 'Le pôle "' . $pole['nom_pole'] . '" a été supprimé.',
                ]));
        }

        session()->setFlashdata('success', 'Le pôle "' . $pole['nom_pole'] . '" a été supprimé.');
        return redirect()->to(site_url('admin/poles'));
    }
}
