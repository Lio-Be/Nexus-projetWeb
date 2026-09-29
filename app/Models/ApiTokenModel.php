<?php

namespace App\Models;

use CodeIgniter\Model;

class ApiTokenModel extends Model
{
    protected $table         = 'api_tokens';
    protected $primaryKey    = 'id_token';
    protected $returnType    = 'array';
    protected $allowedFields = ['id_compte', 'token', 'date_expiration', 'date_creation'];

    public function findValidToken(string $token): ?array
    {
        return $this->where('token', $token)
                    ->where('date_expiration >', date('Y-m-d H:i:s'))
                    ->first();
    }
}
