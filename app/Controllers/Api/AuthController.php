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
        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        if (empty($email) || empty($password)) {
            return $this->failUnauthorized('Email et mot de passe requis.');
        }

        $user = (new UserModel())->findByEmail($email);

        if (! $user || ! password_verify($password, $user['password'])) {
            return $this->failUnauthorized('Email ou mot de passe incorrect.');
        }

        $token    = bin2hex(random_bytes(32));
        $expireAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

        (new ApiTokenModel())->insert([
            'id_compte'       => $user['id_compte'],
            'token'           => $token,
            'date_expiration' => $expireAt,
            'date_creation'   => date('Y-m-d H:i:s'),
        ]);

        return $this->respond([
            'status' => 200,
            'token'  => $token,
            'expire' => $expireAt,
        ]);
    }
}
