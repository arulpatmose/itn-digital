<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ReplaceTimeSlotWithRangeOnBookings extends Migration
{
    public function up()
    {
        // 1. Drop FK only if it exists
        $foreignKeys = $this->db->query("
        SELECT CONSTRAINT_NAME
        FROM information_schema.KEY_COLUMN_USAGE
        WHERE TABLE_NAME = 'bookings'
        AND COLUMN_NAME = 'time_slot_id'
        AND REFERENCED_TABLE_NAME IS NOT NULL
    ")->getResult();

        foreach ($foreignKeys as $fk) {
            $this->db->query("ALTER TABLE bookings DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
        }

        // 2. Drop index if exists (important!)
        try {
            $this->db->query("ALTER TABLE bookings DROP INDEX idx_bookings_time_slot_id");
        } catch (\Throwable $e) {
            // ignore if not exists
        }

        // 3. Drop column if exists
        if ($this->db->fieldExists('time_slot_id', 'bookings')) {
            $this->forge->dropColumn('bookings', 'time_slot_id');
        }

        // 4. Add new columns
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

        // 5. Add FKs
        $this->db->query("
        ALTER TABLE bookings
        ADD CONSTRAINT bookings_start_slot_id_foreign
        FOREIGN KEY (start_slot_id) REFERENCES time_slots (id)
        ON DELETE SET NULL ON UPDATE CASCADE
    ");

        $this->db->query("
        ALTER TABLE bookings
        ADD CONSTRAINT bookings_end_slot_id_foreign
        FOREIGN KEY (end_slot_id) REFERENCES time_slots (id)
        ON DELETE SET NULL ON UPDATE CASCADE
    ");

        // 6. Indexes
        $this->db->query("CREATE INDEX idx_bookings_start_slot_id ON bookings (start_slot_id)");
        $this->db->query("CREATE INDEX idx_bookings_end_slot_id ON bookings (end_slot_id)");
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
