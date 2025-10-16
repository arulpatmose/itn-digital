<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateResourcesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'type_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => '100'],
            'description' => ['type' => 'TEXT', 'null' => true],
            'status'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1, 'comment' => '1=available, 0=unavailable'],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('status');
        $this->forge->addForeignKey('type_id', 'resource_types', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('resources');
    }

    public function down()
    {
        $this->forge->dropTable('resources');
    }
}
