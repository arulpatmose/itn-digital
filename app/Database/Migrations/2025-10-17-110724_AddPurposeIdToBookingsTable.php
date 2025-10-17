<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPurposeIdToBookingsTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('bookings', [
            'purpose_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'resource_id',
            ],
        ]);
        $this->forge->addForeignKey('purpose_id', 'booking_purposes', 'id', 'CASCADE', 'SET NULL');
    }

    public function down()
    {
        $this->forge->dropColumn('bookings', 'purpose_id');
    }
}
