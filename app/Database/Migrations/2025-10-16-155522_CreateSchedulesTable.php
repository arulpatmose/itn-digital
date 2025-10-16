<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSchedulesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'sched_id' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'usched_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'commercial' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => true,
            ],
            'program' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => true,
            ],
            'spot' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => true,
            ],
            'platform' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => true,
            ],
            'total_budget' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => false,
                'default'    => '0.00',
            ],
            'marketing_ex' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'added_by' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
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
            'remarks' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'published' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'default'    => 0,
            ],
        ]);

        // Primary Key
        $this->forge->addKey('sched_id', true);

        // Indexes
        $this->forge->addKey('created_at');
        $this->forge->addKey('published');
        // Note: The foreign key definitions below will automatically create indexes
        // on 'commercial', 'program', 'platform', and 'added_by', so we don't
        // need to add them manually.

        // Foreign Keys
        $this->forge->addForeignKey('commercial', 'commercials', 'com_id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('program', 'programs', 'prog_id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('platform', 'platforms', 'pfm_id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('added_by', 'users', 'id', 'CASCADE', 'SET NULL');
        // Note: Assumes your 'users' table PK is 'id'. Adjust if needed.

        // Create the table
        $this->forge->createTable('schedules');
    }

    public function down()
    {
        $this->forge->dropTable('schedules');
    }
}
