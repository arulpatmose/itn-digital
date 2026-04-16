<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateChipTransactionsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'transaction_type' => [
                'type'       => 'ENUM',
                'constraint' => ['RECEIVE', 'TRANSFER', 'HANDOVER', 'INGEST', 'RETURN'],
            ],
            'from_participant_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'default'    => null,
            ],
            'to_participant_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'default'    => null,
            ],
            'ingest_session_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'default'    => null,
            ],
            'handled_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'remarks' => [
                'type'    => 'TEXT',
                'null'    => true,
                'default' => null,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('transaction_type');
        $this->forge->addKey('from_participant_id');
        $this->forge->addKey('to_participant_id');
        $this->forge->addKey('ingest_session_id');
        $this->forge->addKey('handled_by');

        $this->forge->addForeignKey('from_participant_id', 'participants', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('to_participant_id',   'participants', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('ingest_session_id',   'ingest_sessions', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('handled_by',          'users', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('chip_transactions');
    }

    public function down(): void
    {
        $this->forge->dropTable('chip_transactions');
    }
}
