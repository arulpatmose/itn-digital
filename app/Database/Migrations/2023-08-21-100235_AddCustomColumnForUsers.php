<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCustomColumnForUsers extends Migration
{
    public function up()
    {
        $fields = [
            'first_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => TRUE,
                'after' => 'username'
            ],
            'last_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => TRUE,
            ]
        ];

        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        $fields = [
            'first_name',
            'last_name'
        ];

        $this->forge->dropColumn('users', $fields);
    }
}
