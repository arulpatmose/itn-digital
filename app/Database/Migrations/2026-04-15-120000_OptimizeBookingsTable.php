<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class OptimizeBookingsTable extends Migration
{
    public function up()
    {
        $db = $this->db;
        $database = $db->getDatabase();
        $table = $db->prefixTable('bookings');

        $indexChecks = [
            'idx_bookings_resource_id' => "ALTER TABLE `{$table}` ADD INDEX `idx_bookings_resource_id` (`resource_id`)",
            'idx_bookings_time_slot_id' => "ALTER TABLE `{$table}` ADD INDEX `idx_bookings_time_slot_id` (`time_slot_id`)",
            'idx_bookings_user_id' => "ALTER TABLE `{$table}` ADD INDEX `idx_bookings_user_id` (`user_id`)",
            'idx_bookings_lookup' => "ALTER TABLE `{$table}` ADD INDEX `idx_bookings_lookup` (`resource_id`, `booking_date`, `time_slot_id`, `status`)",
            'idx_bookings_purpose_id' => "ALTER TABLE `{$table}` ADD INDEX `idx_bookings_purpose_id` (`purpose_id`)",
        ];

        foreach ($indexChecks as $indexName => $sql) {
            $exists = $db->query(
                'SELECT 1 FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND INDEX_NAME = ? LIMIT 1',
                [$database, $table, $indexName]
            )->getFirstRow();

            if (!$exists) {
                $db->query($sql);
            }
        }

        $fkExists = $db->query(
            'SELECT 1 FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ? AND REFERENCED_TABLE_NAME = ? LIMIT 1',
            [$database, $table, 'purpose_id', $db->prefixTable('booking_purposes')]
        )->getFirstRow();

        if (!$fkExists) {
            $db->query(
                "ALTER TABLE `{$table}` ADD CONSTRAINT `fk_bookings_purpose_id` FOREIGN KEY (`purpose_id`) REFERENCES `"
                . $db->prefixTable('booking_purposes')
                . "` (`id`) ON DELETE SET NULL ON UPDATE CASCADE"
            );
        }
    }

    public function down()
    {
        $db = $this->db;
        $table = $db->prefixTable('bookings');

        $indexes = [
            'idx_bookings_resource_id',
            'idx_bookings_time_slot_id',
            'idx_bookings_user_id',
            'idx_bookings_lookup',
            'idx_bookings_purpose_id',
        ];

        foreach ($indexes as $indexName) {
            try {
                $db->query("ALTER TABLE `{$table}` DROP INDEX `{$indexName}`");
            } catch (\Throwable $e) {
            }
        }

        try {
            $db->query("ALTER TABLE `{$table}` DROP FOREIGN KEY `fk_bookings_purpose_id`");
        } catch (\Throwable $e) {
        }
    }
}
