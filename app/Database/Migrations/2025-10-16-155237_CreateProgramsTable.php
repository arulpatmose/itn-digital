<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProgramsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'prog_id' => [
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
            'type' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false, // Equivalent to NOT NULL
                'default'    => 0,
            ],
            'thumbnail' => [
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
        $this->forge->addKey('prog_id', true);

        // Creates the 'programs' table
        $this->forge->createTable('programs');
    }

    public function down()
    {
        // Drops the 'programs' table
        $this->forge->dropTable('programs');
    }
}
