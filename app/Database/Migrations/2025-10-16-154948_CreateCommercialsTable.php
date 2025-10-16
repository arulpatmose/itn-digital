<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCommercialsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'com_id' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'ucom_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'duration' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'format' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => true,
            ],
            'category' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'sub_category' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'client' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
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
            'link' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
        ]);

        // Primary Key
        $this->forge->addKey('com_id', true);

        // Foreign Keys
        // The foreign key declaration also creates an index automatically.
        $this->forge->addForeignKey('client', 'clients', 'client_id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('format', 'formats', 'format_id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('added_by', 'users', 'id', 'CASCADE', 'SET NULL');
        // Note: Assuming your 'users' table has a primary key named 'id'. Adjust if necessary.

        // Create the table
        $this->forge->createTable('commercials');
    }

    public function down()
    {
        // To drop a table with foreign keys, you might need to disable checks temporarily
        // although forge->dropTable should handle this. If you get errors, uncomment the lines below.
        // $this->db->disableForeignKeyChecks();
        $this->forge->dropTable('commercials');
        // $this->db->enableForeignKeyChecks();
    }
}
