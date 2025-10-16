<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePlatformsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'pfm_id' => [
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
            'channel' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
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
        $this->forge->addKey('pfm_id', true);

        // Creates the 'platforms' table
        $this->forge->createTable('platforms');
    }

    public function down()
    {
        // Drops the 'platforms' table
        $this->forge->dropTable('platforms');
    }
}
