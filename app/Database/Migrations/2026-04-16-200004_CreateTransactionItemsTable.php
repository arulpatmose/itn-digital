<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTransactionItemsTable extends Migration
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
            'transaction_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'chip_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['transaction_id', 'chip_id']);
        $this->forge->addKey('chip_id');

        $this->forge->addForeignKey('transaction_id', 'chip_transactions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('chip_id',        'chips',             'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('transaction_items');
    }

    public function down(): void
    {
        $this->forge->dropTable('transaction_items');
    }
}
