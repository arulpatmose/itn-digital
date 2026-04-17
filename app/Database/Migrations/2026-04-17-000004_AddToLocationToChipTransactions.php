<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddToLocationToChipTransactions extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('chip_transactions', [
            'to_location' => [
                'type'       => 'ENUM',
                'constraint' => ['producer', 'digital_unit', 'library', 'ingest'],
                'null'       => true,
                'default'    => null,
                'after'      => 'to_participant_id',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('chip_transactions', 'to_location');
    }
}
