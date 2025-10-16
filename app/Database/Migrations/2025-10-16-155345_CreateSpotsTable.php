<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSpotsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'spot_id' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'priority' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false, // NOT NULL
                'default'    => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        // Defines the primary key
        $this->forge->addKey('spot_id', true);

        // Creates the 'spots' table
        $this->forge->createTable('spots');
    }

    public function down()
    {
        // Drops the 'spots' table
        $this->forge->dropTable('spots');
    }
}
