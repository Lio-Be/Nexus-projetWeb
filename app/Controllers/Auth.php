<?php
namespace App\Controllers;
use App\Controllers\BaseController;
use App\Models\UserModel;

class Auth extends BaseController
{
    // Méthode 1 : Afficher la page de login
    public function login()
    {
        // Si déjà connecté, rediriger vers le bon dashboard
        if (session()->get('isLoggedIn')) {
            $role = session()->get('role');
            if ($role === 'Admin')     return redirect()->to('/admin/dashboard');
            if ($role === 'Formateur') return redirect()->to('/formateur/dashboard');
            return redirect()->to('/etudiant/dashboard');
        }

        return view('auth/login', ['title' => 'Connexion']);
    }
    
    // Méthode 2 : 
    public function attemptLogin()
    {
        $rules = [
            'email'    => [
                'rules'  => 'required|valid_email',
                'errors' => [
                    'required'    => 'L\'adresse email est obligatoire.',
                    'valid_email' => 'L\'adresse email n\'est pas valide.',
                ],
            ],
            'password' => [
                'rules'  => 'required|min_length[8]',
                'errors' => [
                    'required'   => 'Le mot de passe est obligatoire.',
                    'min_length' => 'Le mot de passe doit contenir au moins 8 caractères.',
                ],
            ],
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', current($this->validator->getErrors()));
        }

        $userModel = new UserModel();
        $email     = $this->request->getPost('email');
        $password  = $this->request->getPost('password');
        $user      = $userModel->findByEmail($email);

        if (! $user || ! password_verify($password, $user['password'])) {
            return redirect()->back()->with('error', 'Email ou mot de passe incorrect');
        }

        session()->set([
            'id_compte'  => $user['id_compte'],
            'nom'        => $user['nom'],
            'prenom'     => $user['prenom'],
            'email'      => $user['email'],
            'role'       => $user['role'],
            'id_role'    => $user['id_role'],
            'isLoggedIn' => true,
        ]);

        if ($user['role'] === 'Admin') {
            return redirect()->to('/admin/dashboard');
        } elseif ($user['role'] === 'Formateur') {
            return redirect()->to('/formateur/dashboard');
        } else {
            return redirect()->to('/etudiant/dashboard');
        }
    }
    
    // Méthode 3 : 
    public function logout()
    {
    // 1. Détruire la session  
    session()->destroy();
    
    // 2. Rediriger vers le login 
    return redirect()->to('/login');
    }


    public function register()
{
    return view('auth/register', ['title' => 'Inscription']);
}

public function attemptRegister()
{
    // 1. VALIDATION DES DONNÉES
    $validation = \Config\Services::validation(); // validation est un service de codeigniter 4 et se transforme en objet pour verifier les données
    
    $validation->setRules([
        'nom'       => 'required|min_length[2]|max_length[50]', // entre '' regle1|regle2|regle3|,...
        'prenom'    => 'required|min_length[2]|max_length[50]',
        'email'     => 'required|valid_email|max_length[100]|is_unique[comptes.email]', //-- CI4 exécute automatiquement cette requête : SELECT COUNT(*) FROM comptes WHERE email = 'test@test.com';
        'password'  => 'required|min_length[8]',
        'password_confirm' => 'required|matches[password]'
    ]);
    
    if (!$validation->withRequest($this->request)->run()) {
        return redirect()->back()->withInput()->with('errors', $validation->getErrors());
    } // redirect -> Créer une redirection HTTP , Fonction helper de CI4
    // back -> Retourner à la page précédente
    // withInput -> Conserve les valeurs du formulaire,  l'utilisateur ne doit PAS retaper tout !
    
    // 2. PRÉPARATION DES DONNÉES
    $data = [
        'nom'       => $this->request->getPost('nom'),
        'prenom'    => $this->request->getPost('prenom'),
        'email'     => $this->request->getPost('email'),
        'password'  => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
        'adresse'   => $this->request->getPost('adresse'),
        'code_postal' => $this->request->getPost('code_postal'),
        'date_naissance' => $this->request->getPost('date_naissance'),
        
    ];
    
    // 3. INSERTION DANS COMPTES
    $userModel = new UserModel();
    $inserted = $userModel->insert($data);
    
    if (!$inserted) {
        return redirect()->back()->withInput()->with('error', 'Erreur lors de la création du compte');
    }
    
    // 4. RÉCUPÉRER L'ID DU COMPTE CRÉÉ
    $id_compte = $userModel->getInsertID(); // va chercher le dernier ID creer comme en sql : SELECT LAST_INSERT_ID();
    
    // 5. ASSIGNER LE RÔLE "ETUDIANT" (id_role = 2)
    $db = \Config\Database::connect(); // se connecte a la db
    $db->table('compte_roles')->insert([
        'id_compte' => $id_compte,
        'id_role'   => 2,
        'date_attribution' => date('Y-m-d H:i:s')
    ]);
    
    // 6. REDIRECTION VERS LOGIN AVEC MESSAGE DE SUCCÈS
    return redirect()->to('/login')->with('success', 'Compte créé avec succès ! Vous pouvez maintenant vous connecter.');
}
}