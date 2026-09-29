<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateApiTokensTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id_token' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'id_compte' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'token' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
            ],
            'date_expiration' => [
                'type' => 'DATETIME',
            ],
            'date_creation' => [
                'type' => 'DATETIME',
            ],
        ]);

        $this->forge->addPrimaryKey('id_token');
        $this->forge->addUniqueKey('token');
        $this->forge->addKey('id_compte');

        $this->forge->createTable('api_tokens');
    }

    public function down(): void
    {
        $this->forge->dropTable('api_tokens', true);
    }
}
