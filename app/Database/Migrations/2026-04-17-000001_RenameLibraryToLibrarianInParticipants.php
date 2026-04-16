<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameLibraryToLibrarianInParticipants extends Migration
{
    public function up(): void
    {
        // 1. Update any existing rows that have type = 'library' → 'librarian'
        $this->db->query("UPDATE participants SET type = 'librarian' WHERE type = 'library'");

        // 2. Alter the ENUM column to replace 'library' with 'librarian'
        $this->db->query("
            ALTER TABLE participants
            MODIFY COLUMN `type` ENUM('ingestor', 'producer', 'librarian') NOT NULL
        ");
    }

    public function down(): void
    {
        $this->db->query("UPDATE participants SET type = 'library' WHERE type = 'librarian'");

        $this->db->query("
            ALTER TABLE participants
            MODIFY COLUMN `type` ENUM('staff', 'producer', 'library') NOT NULL
        ");
    }
}
