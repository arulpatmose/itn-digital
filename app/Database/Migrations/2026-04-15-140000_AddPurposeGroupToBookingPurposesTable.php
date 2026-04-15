<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPurposeGroupToBookingPurposesTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('booking_purposes', [
            'purpose_group' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'name',
            ],
        ]);

        $this->db->table('booking_purposes')
            ->set('purpose_group', 'General')
            ->where('purpose_group IS NULL', null, false)
            ->update();

        $this->db->query('ALTER TABLE `booking_purposes` ADD INDEX `idx_booking_purposes_group` (`purpose_group`)');
    }

    public function down()
    {
        try {
            $this->db->query('ALTER TABLE `booking_purposes` DROP INDEX `idx_booking_purposes_group`');
        } catch (\Throwable $e) {
        }

        $this->forge->dropColumn('booking_purposes', 'purpose_group');
    }
}
