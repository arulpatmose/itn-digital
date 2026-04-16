<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ReplaceTimeSlotWithRangeOnBookings extends Migration
{
    public function up()
    {
        // This migration was superseded in production by the state where
        // time_slot_id had already been replaced with start_slot_id / end_slot_id
        // (via an earlier run). The final cleanup to start_time / end_time is
        // handled by 2026-04-16-000000_SimplifyBookingTimeColumns.
        //
        // Original intent: replace time_slot_id with start_slot_id + end_slot_id.
        $this->db->query('ALTER TABLE bookings DROP FOREIGN KEY fk_bookings_time_slot_id');
        $this->forge->dropColumn('bookings', 'time_slot_id');

        $this->forge->addColumn('bookings', [
            'start_slot_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
                'default'  => null,
                'after'    => 'purpose_id',
            ],
            'end_slot_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
                'default'  => null,
                'after'    => 'start_slot_id',
            ],
        ]);

        $this->db->query('
            ALTER TABLE bookings
                ADD CONSTRAINT bookings_start_slot_id_foreign
                FOREIGN KEY (start_slot_id) REFERENCES time_slots (id) ON DELETE SET NULL ON UPDATE CASCADE
        ');
        $this->db->query('
            ALTER TABLE bookings
                ADD CONSTRAINT bookings_end_slot_id_foreign
                FOREIGN KEY (end_slot_id) REFERENCES time_slots (id) ON DELETE SET NULL ON UPDATE CASCADE
        ');
        $this->db->query('CREATE INDEX idx_bookings_start_slot_id ON bookings (start_slot_id)');
        $this->db->query('CREATE INDEX idx_bookings_end_slot_id   ON bookings (end_slot_id)');
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

        // Remove TIME columns from bookings
        $this->forge->dropColumn('bookings', 'start_time');
        $this->forge->dropColumn('bookings', 'end_time');

        // Restore the old FK column
        $this->forge->addColumn('bookings', [
            'time_slot_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
                'default'  => null,
            ],
        ]);

        $this->db->query('
            ALTER TABLE bookings
                ADD CONSTRAINT fk_bookings_time_slot_id
                FOREIGN KEY (time_slot_id) REFERENCES time_slots (id) ON DELETE CASCADE ON UPDATE CASCADE
        ');
    }
}
