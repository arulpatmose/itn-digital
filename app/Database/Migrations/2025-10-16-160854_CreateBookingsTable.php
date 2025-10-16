<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBookingsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'resource_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'time_slot_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'booking_date'     => ['type' => 'DATE'],
            'status'           => ['type' => 'ENUM', 'constraint' => ['pending', 'approved', 'rejected', 'cancelled'], 'default' => 'pending'],
            'remarks'          => ['type' => 'TEXT', 'null' => true],
            'approval_remarks' => ['type' => 'TEXT', 'null' => true],
            'approved_by'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('booking_date');
        $this->forge->addKey('status');

        // Foreign Keys
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('resource_id', 'resources', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('time_slot_id', 'time_slots', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('approved_by', 'users', 'id', 'SET NULL', 'CASCADE');

        $this->forge->createTable('bookings');
    }

    public function down()
    {
        $this->forge->dropTable('bookings');
    }
}
