<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateChipsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'chip_type' => [
                'type'       => 'ENUM',
                'constraint' => ['SXS', 'SD', 'MicroSD', 'Other'],
            ],
            'chip_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'notes' => [
                'type'    => 'TEXT',
                'null'    => true,
                'default' => null,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('chip_code');
        $this->forge->addKey('chip_type');

        $this->forge->createTable('chips');
    }

    public function down(): void
    {
        $this->forge->dropTable('chips');
    }
}
