<?php

namespace App\Controllers\Api;

use App\Models\ApiTokenModel;
use App\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;

class AuthController extends ResourceController
{
    protected $format = 'json';

    public function login()
    {
        // getVar() lit le corps JSON (Content-Type: application/json)
        // aussi bien que les données de formulaire.
        $email    = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        if (! is_string($email) || ! is_string($password) || $email === '' || $password === '') {
            return $this->failUnauthorized('Email et mot de passe requis.');
        }

        $user = (new UserModel())->findByEmail($email);

        if (! $user || ! password_verify($password, $user['password'])) {
            return $this->failUnauthorized('Email ou mot de passe incorrect.');
        }

        $token = (new ApiTokenModel())->creerToken((int) $user['id_compte']);

        return $this->respond([
            'status' => 200,
            'token'  => $token['token'],
            'expire' => $token['expire'],
        ]);
    }
}
