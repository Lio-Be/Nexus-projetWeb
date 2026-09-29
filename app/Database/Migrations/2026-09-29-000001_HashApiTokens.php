<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Les tokens API ne sont plus stockés en clair : seule leur empreinte SHA-256
 * est conservée. Les tokens existants (en clair) sont supprimés, les clients
 * doivent se reconnecter.
 */
class HashApiTokens extends Migration
{
    public function up(): void
    {
        $this->db->table('api_tokens')->truncate();

        $this->forge->modifyColumn('api_tokens', [
            'token' => [
                'name'       => 'token_hash',
                'type'       => 'CHAR',
                'constraint' => 64,
            ],
        ]);
    }

    public function down(): void
    {
        $this->db->table('api_tokens')->truncate();

        $this->forge->modifyColumn('api_tokens', [
            'token_hash' => [
                'name'       => 'token',
                'type'       => 'VARCHAR',
                'constraint' => 64,
            ],
        ]);
    }
}
