<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SimplifyBookingTimeColumns extends Migration
{
    public function up()
    {
        // Fresh system — clear any test bookings so FK constraints don't block us
        $this->db->query('TRUNCATE TABLE bookings');

        // Drop FK constraints referencing time_slots
        $this->db->query('ALTER TABLE bookings DROP FOREIGN KEY bookings_start_slot_id_foreign');
        $this->db->query('ALTER TABLE bookings DROP FOREIGN KEY bookings_end_slot_id_foreign');

        // Drop the slot ID indexes and columns
        $this->db->query('DROP INDEX idx_bookings_start_slot_id ON bookings');
        $this->db->query('DROP INDEX idx_bookings_end_slot_id   ON bookings');
        $this->forge->dropColumn('bookings', 'start_slot_id');
        $this->forge->dropColumn('bookings', 'end_slot_id');

        // Add direct TIME columns
        $this->forge->addColumn('bookings', [
            'start_time' => [
                'type'  => 'TIME',
                'null'  => false,
                'after' => 'purpose_id',
            ],
            'end_time' => [
                'type'  => 'TIME',
                'null'  => false,
                'after' => 'start_time',
            ],
        ]);

        // Drop time_slots — no longer needed
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');
        $this->forge->dropTable('time_slots', true);
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
    }

    public function down()
    {
        // Recreate time_slots
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'label'      => ['type' => 'VARCHAR', 'constraint' => '100'],
            'start_time' => ['type' => 'TIME'],
            'end_time'   => ['type' => 'TIME'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('time_slots');

        // Remove direct TIME columns and restore slot ID columns
        $this->forge->dropColumn('bookings', 'start_time');
        $this->forge->dropColumn('bookings', 'end_time');

        $this->forge->addColumn('bookings', [
            'start_slot_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true, 'default' => null],
            'end_slot_id'   => ['type' => 'INT', 'unsigned' => true, 'null' => true, 'default' => null],
        ]);

        $this->db->query('ALTER TABLE bookings ADD CONSTRAINT bookings_start_slot_id_foreign FOREIGN KEY (start_slot_id) REFERENCES time_slots (id) ON DELETE SET NULL ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE bookings ADD CONSTRAINT bookings_end_slot_id_foreign   FOREIGN KEY (end_slot_id)   REFERENCES time_slots (id) ON DELETE SET NULL ON UPDATE CASCADE');
        $this->db->query('CREATE INDEX idx_bookings_start_slot_id ON bookings (start_slot_id)');
        $this->db->query('CREATE INDEX idx_bookings_end_slot_id   ON bookings (end_slot_id)');
    }
}
