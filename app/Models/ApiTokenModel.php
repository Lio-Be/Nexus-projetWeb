<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Tokens d'accès à l'API.
 *
 * Seule l'empreinte SHA-256 du token est stockée : en cas de fuite de la base,
 * les tokens ne sont pas réutilisables. Le token en clair n'est connu que du
 * client, qui le reçoit une seule fois au login.
 */
class ApiTokenModel extends Model
{
    public const DUREE_VALIDITE = '+24 hours';

    protected $table         = 'api_tokens';
    protected $primaryKey    = 'id_token';
    protected $returnType    = 'array';
    protected $allowedFields = ['id_compte', 'token_hash', 'date_expiration', 'date_creation'];

    /**
     * Crée un token pour le compte et retourne sa valeur en clair (non stockée).
     *
     * @return array{token: string, expire: string}
     */
    public function creerToken(int $idCompte): array
    {
        $token    = bin2hex(random_bytes(32));
        $expireAt = date('Y-m-d H:i:s', strtotime(self::DUREE_VALIDITE));

        $this->insert([
            'id_compte'       => $idCompte,
            'token_hash'      => self::hasher($token),
            'date_expiration' => $expireAt,
            'date_creation'   => date('Y-m-d H:i:s'),
        ]);

        return ['token' => $token, 'expire' => $expireAt];
    }

    public function findValidToken(string $token): ?array
    {
        return $this->where('token_hash', self::hasher($token))
                    ->where('date_expiration >', date('Y-m-d H:i:s'))
                    ->first();
    }

    public static function hasher(string $token): string
    {
        return hash('sha256', $token);
    }
}
