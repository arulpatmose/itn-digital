<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateScheduleItemsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'scd_id' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'sched_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => true,
            ],
            'sched_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'spot' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => true,
            ],
            'remarks' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'link' => [
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
            'added_by' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => true,
            ],
            'updated_by' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => true,
            ],
        ]);

        // Primary Key
        $this->forge->addKey('scd_id', true);

        // Indexes
        // We only need to add indexes for columns that are NOT foreign keys,
        // as addForeignKey() creates an index automatically.
        $this->forge->addKey('deleted_at');
        $this->forge->addKey('published');

        // Foreign Keys
        $this->forge->addForeignKey('sched_id', 'schedules', 'sched_id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('spot', 'spots', 'spot_id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('added_by', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('updated_by', 'users', 'id', 'CASCADE', 'SET NULL');
        // Note: Assumes your 'users' table PK is 'id'. Adjust if needed.

        // Create the table
        $this->forge->createTable('schedule_items');
    }

    public function down()
    {
        $this->forge->dropTable('schedule_items');
    }
}
