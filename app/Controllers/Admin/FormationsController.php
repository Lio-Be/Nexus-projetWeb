<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\FormationModel;
use App\Models\PoleModel;

class FormationsController extends BaseController
{
    private FormationModel $formationModel;
    private PoleModel      $poleModel;

    public function __construct()
    {
        $this->formationModel = new FormationModel();
        $this->poleModel      = new PoleModel();
    }

    /**
     * Affiche la liste de toutes les formations avec leur pôle.
     */
    public function index(): string
    {
        $data = [
            'title'      => 'Gestion des Formations',
            'formations' => $this->formationModel->getAllWithPole(),
        ];

        return view('admin/formations/index', $data);
    }

    /**
     * Affiche le formulaire de création d'une formation.
     * Charge la liste des pôles pour le <select> et restitue les anciennes valeurs après erreur.
     */
    public function create(): string
    {
        $data = [
            'title'  => 'Nouvelle Formation',
            'poles'  => $this->poleModel->findAll(),
            'errors' => session()->getFlashdata('errors') ?? [],
            'old'    => session()->getFlashdata('old')    ?? [],
        ];

        return view('admin/formations/create', $data);
    }

    /**
     * Traite le formulaire de création : valide les champs texte et l'image (optionnelle),
     * déplace l'image dans /public/uploads/formations/ et insère la formation en BDD.
     */
    public function store()
    {
        $rules = [
            'titre' => [
                'rules'  => 'required|max_length[150]|is_unique[formations.titre]',
                'errors' => [
                    'required'   => 'Le titre est obligatoire.',
                    'max_length' => 'Le titre ne peut pas dépasser 150 caractères.',
                    'is_unique'  => 'Une formation avec ce titre existe déjà.',
                ],
            ],
            'description' => ['rules' => 'permit_empty', 'errors' => []],
            'duree' => [
                'rules'  => 'permit_empty|integer|greater_than[0]',
                'errors' => [
                    'integer'      => 'La durée doit être un nombre entier.',
                    'greater_than' => 'La durée doit être supérieure à 0.',
                ],
            ],
            'prix' => [
                'rules'  => 'permit_empty|decimal|greater_than_equal_to[0]',
                'errors' => [
                    'decimal'               => 'Le prix doit être un nombre décimal.',
                    'greater_than_equal_to' => 'Le prix ne peut pas être négatif.',
                ],
            ],
            'prerequis'  => ['rules' => 'permit_empty', 'errors' => []],
            'id_pole'    => [
                'rules'  => 'required|integer',
                'errors' => [
                    'required' => 'Vous devez sélectionner un pôle.',
                    'integer'  => 'Le pôle sélectionné est invalide.',
                ],
            ],
        ];

        $file = $this->request->getFile('image');

        if ($file !== null && $file->isValid() && !$file->hasMoved()) {
            $rules['image'] = [
                'rules'  => 'max_size[image,2048]|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png,image/webp]',
                'errors' => [
                    'max_size' => "L'image ne doit pas dépasser 2 Mo.",
                    'is_image' => "Le fichier sélectionné n'est pas une image valide.",
                    'mime_in'  => 'Formats acceptés : JPG, PNG, WebP.',
                ],
            ];
        }

        if (! $this->validate($rules)) {
            session()->setFlashdata('errors', $this->validator->getErrors());
            session()->setFlashdata('old', $this->request->getPost());
            return redirect()->to(site_url('admin/formations/create'));
        }

        $imageName = null;

        if ($file !== null && $file->isValid() && !$file->hasMoved()) {
            $imageName = strtolower($file->getRandomName());
            $file->move(ROOTPATH . 'public/uploads/formations/', $imageName);
        }

        $data = [
            'titre'       => $this->request->getPost('titre'),
            'description' => $this->request->getPost('description'),
            'duree'       => $this->request->getPost('duree') ?: null,
            'prix'        => $this->request->getPost('prix')  ?: null,
            'image'       => $imageName,
            'prerequis'   => $this->request->getPost('prerequis'),
            'id_pole'     => $this->request->getPost('id_pole'),
        ];

        $this->formationModel->insert($data);

        session()->setFlashdata('success', 'La formation "' . $data['titre'] . '" a été créée avec succès.');
        return redirect()->to(site_url('admin/formations'));
    }

    /**
     * Affiche le formulaire de modification d'une formation existante.
     * Lève une 404 si la formation n'existe pas.
     *
     * @param int $id
     */
    public function edit(int $id): string
    {
        $formation = $this->formationModel->getWithPole($id);

        if ($formation === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Formation #$id introuvable.");
        }

        $data = [
            'title'     => 'Modifier : ' . $formation['titre'],
            'formation' => $formation,
            'poles'     => $this->poleModel->findAll(),
            'errors'    => session()->getFlashdata('errors') ?? [],
            'old'       => session()->getFlashdata('old')    ?? [],
        ];

        return view('admin/formations/edit', $data);
    }

    /**
     * Traite le formulaire de modification : valide les données, remplace l'image si une
     * nouvelle est fournie (supprime l'ancienne du disque) et met à jour la BDD.
     *
     * @param int $id
     */
    public function update(int $id)
    {
        $formation = $this->formationModel->find($id);

        if ($formation === null) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Formation #$id introuvable.");
        }

        $rules = [
            'titre' => [
                'rules'  => "required|max_length[150]|is_unique[formations.titre,id_formation,$id]",
                'errors' => [
                    'required'   => 'Le titre est obligatoire.',
                    'max_length' => 'Le titre ne peut pas dépasser 150 caractères.',
                    'is_unique'  => 'Une autre formation avec ce titre existe déjà.',
                ],
            ],
            'description' => ['rules' => 'permit_empty', 'errors' => []],
            'duree' => [
                'rules'  => 'permit_empty|integer|greater_than[0]',
                'errors' => [
                    'integer'      => 'La durée doit être un nombre entier.',
                    'greater_than' => 'La durée doit être supérieure à 0.',
                ],
            ],
            'prix' => [
                'rules'  => 'permit_empty|decimal|greater_than_equal_to[0]',
                'errors' => [
                    'decimal'               => 'Le prix doit être un nombre décimal.',
                    'greater_than_equal_to' => 'Le prix ne peut pas être négatif.',
                ],
            ],
            'prerequis' => ['rules' => 'permit_empty', 'errors' => []],
            'id_pole'   => [
                'rules'  => 'required|integer',
                'errors' => [
                    'required' => 'Vous devez sélectionner un pôle.',
                    'integer'  => 'Le pôle sélectionné est invalide.',
                ],
            ],
        ];

        $file             = $this->request->getFile('image');
        $newImageProvided = ($file !== null && $file->isValid() && !$file->hasMoved());

        if ($newImageProvided) {
            $rules['image'] = [
                'rules'  => 'max_size[image,2048]|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png,image/webp]',
                'errors' => [
                    'max_size' => "L'image ne doit pas dépasser 2 Mo.",
                    'is_image' => "Le fichier sélectionné n'est pas une image valide.",
                    'mime_in'  => 'Formats acceptés : JPG, PNG, WebP.',
                ],
            ];
        }

        if (! $this->validate($rules)) {
            session()->setFlashdata('errors', $this->validator->getErrors());
            session()->setFlashdata('old', $this->request->getPost());
            return redirect()->to(site_url("admin/formations/edit/$id"));
        }

        $imageName = $formation['image'];

        if ($newImageProvided) {
            if ($formation['image'] !== null) {
                $oldPath = ROOTPATH . 'public/uploads/formations/' . $formation['image'];
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $imageName = strtolower($file->getRandomName());
            $file->move(ROOTPATH . 'public/uploads/formations/', $imageName);
        }

        $data = [
            'titre'       => $this->request->getPost('titre'),
            'description' => $this->request->getPost('description'),
            'duree'       => $this->request->getPost('duree') ?: null,
            'prix'        => $this->request->getPost('prix')  ?: null,
            'image'       => $imageName,
            'prerequis'   => $this->request->getPost('prerequis'),
            'id_pole'     => $this->request->getPost('id_pole'),
        ];

        $this->formationModel->update($id, $data);

        session()->setFlashdata('success', 'La formation "' . $data['titre'] . '" a été modifiée avec succès.');
        return redirect()->to(site_url('admin/formations'));
    }

    /**
     * Supprime une formation et son image physique si elle existe.
     * Supporte les requêtes AJAX (retourne du JSON) et les requêtes classiques.
     *
     * @param int $id
     */
    public function delete(int $id)
    {
        $formation = $this->formationModel->find($id);

        if ($formation === null) {
            if ($this->request->isAJAX()) {
                return $this->response
                    ->setContentType('application/json')
                    ->setBody(json_encode(['success' => false, 'message' => "Formation introuvable."]));
            }
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Formation #$id introuvable.");
        }

        if ($formation['image'] !== null) {
            $imagePath = ROOTPATH . 'public/uploads/formations/' . $formation['image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $this->formationModel->delete($id);

        if ($this->request->isAJAX()) {
            return $this->response
                ->setContentType('application/json')
                ->setBody(json_encode([
                    'success' => true,
                    'message' => 'La formation "' . $formation['titre'] . '" a été supprimée.',
                ]));
        }

        session()->setFlashdata('success', 'La formation "' . $formation['titre'] . '" a été supprimée.');
        return redirect()->to(site_url('admin/formations'));
    }
}
