<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTimeSlotsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'label'      => ['type' => 'VARCHAR', 'constraint' => '100'],
            'start_time' => ['type' => 'TIME'],
            'end_time'   => ['type' => 'TIME'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['start_time', 'end_time']);
        $this->forge->createTable('time_slots');
    }

    public function down()
    {
        $this->forge->dropTable('time_slots');
    }
}
