<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFormatsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'format_id' => [
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
            'code' => [
                'type'       => 'VARCHAR',
                'constraint' => '11',
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
        $this->forge->addKey('format_id', true);

        // Creates the 'formats' table
        $this->forge->createTable('formats');
    }

    public function down()
    {
        // Drops the 'formats' table
        $this->forge->dropTable('formats');
    }
}
