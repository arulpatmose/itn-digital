<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MoveSpotToScheduleItems extends Migration
{
    public function up()
    {
        // Add the spot column to the schedule_items table
        $this->forge->addColumn('schedule_items', [
            'spot' => [
                'type' => 'INT',
                'null' => TRUE,
            ],
        ]);

        // Load the database connection
        $db = \Config\Database::connect();

        // Get all schedules with their spot values
        $schedules = $db->table('schedules')
            ->select('sched_id, spot')
            ->get()
            ->getResultArray();

        // Loop through each schedule and update the corresponding schedule_items
        foreach ($schedules as $schedule) {
            $db->table('schedule_items')
                ->where('sched_id', $schedule['sched_id'])
                ->update(['spot' => $schedule['spot']]);
        }

        // Optionally, drop the spot column from schedules table
        // $this->forge->dropColumn('schedules', 'spot');
    }

    public function down()
    {
        // Drop the spot column from the schedule_items table
        $this->forge->dropColumn('schedule_items', 'spot');

        // Optionally, add the spot column back to schedules table and restore data if needed
        // $this->forge->addColumn('schedules', [
        //     'spot' => [
        //         'type' => 'INT',
        //         'null' => TRUE,
        //     ],
        // ]);

        // Restore the spot data if needed
        // $db = \Config\Database::connect();
        // $scheduleItems = $db->table('schedule_items')
        //                     ->select('sched_id, spot')
        //                     ->get()
        //                     ->getResultArray();
        // foreach ($scheduleItems as $item) {
        //     $db->table('schedules')
        //        ->where('sched_id', $item['sched_id'])
        //        ->update(['spot' => $item['spot']]);
        // }
    }
}
