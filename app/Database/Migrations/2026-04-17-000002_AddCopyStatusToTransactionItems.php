<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCopyStatusToTransactionItems extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('transaction_items', [
            'copy_status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'done'],
                'default'    => 'pending',
                'after'      => 'chip_id',
            ],
            'copied_at' => [
                'type'  => 'DATETIME',
                'null'  => true,
                'after' => 'copy_status',
            ],
            'copied_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'copied_at',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('transaction_items', ['copy_status', 'copied_at', 'copied_by']);
    }
}
