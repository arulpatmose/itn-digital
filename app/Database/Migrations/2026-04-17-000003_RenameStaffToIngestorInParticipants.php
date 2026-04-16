<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameStaffToIngestorInParticipants extends Migration
{
    public function up(): void
    {
        $this->db->query("UPDATE participants SET type = 'ingestor' WHERE type = 'staff'");

        $this->db->query("
            ALTER TABLE participants
            MODIFY COLUMN `type` ENUM('ingestor', 'producer', 'librarian') NOT NULL
        ");
    }

    public function down(): void
    {
        $this->db->query("UPDATE participants SET type = 'staff' WHERE type = 'ingestor'");

        $this->db->query("
            ALTER TABLE participants
            MODIFY COLUMN `type` ENUM('staff', 'producer', 'librarian') NOT NULL
        ");
    }
}
