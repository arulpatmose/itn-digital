<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class NormalizeBookingPurposeGroups extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('booking_purpose_groups')) {
            $this->forge->addField([
                'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'name'        => ['type' => 'VARCHAR', 'constraint' => 100],
                'slug'        => ['type' => 'VARCHAR', 'constraint' => 100],
                'description' => ['type' => 'TEXT', 'null' => true],
                'sort_order'  => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
                'is_active'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'created_at'  => ['type' => 'DATETIME', 'null' => true],
                'updated_at'  => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('slug');
            $this->forge->addKey('sort_order');
            $this->forge->createTable('booking_purpose_groups');
        }

        $groups = [
            ['name' => 'Production', 'slug' => 'production', 'description' => 'Studio, broadcast, and production activities.', 'sort_order' => 10],
            ['name' => 'Audio', 'slug' => 'audio', 'description' => 'Audio-focused recording and dubbing activities.', 'sort_order' => 20],
            ['name' => 'Preparation', 'slug' => 'preparation', 'description' => 'Setup, testing, and rehearsal work.', 'sort_order' => 30],
            ['name' => 'Training', 'slug' => 'training', 'description' => 'Training sessions, workshops, and learning events.', 'sort_order' => 40],
            ['name' => 'Meetings & Events', 'slug' => 'meetings-events', 'description' => 'Meetings, presentations, and events.', 'sort_order' => 50],
            ['name' => 'Field Operations', 'slug' => 'field-operations', 'description' => 'Outdoor and field production work.', 'sort_order' => 60],
            ['name' => 'Maintenance', 'slug' => 'maintenance', 'description' => 'Maintenance, repair, and inspection tasks.', 'sort_order' => 70],
            ['name' => 'Other', 'slug' => 'other', 'description' => 'Fallback group for uncategorized purposes.', 'sort_order' => 80],
        ];

        $groupTable = $this->db->table('booking_purpose_groups');
        foreach ($groups as $group) {
            $exists = $groupTable->where('slug', $group['slug'])->get()->getFirstRow('array');
            if (! $exists) {
                $groupTable->insert(array_merge($group, ['is_active' => 1]));
            }
        }

        if (! $this->db->fieldExists('group_id', 'booking_purposes')) {
            $this->forge->addColumn('booking_purposes', [
                'group_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => $this->db->fieldExists('purpose_group', 'booking_purposes') ? 'purpose_group' : 'name',
                ],
            ]);
        }

        $groupIds = [];
        $groupRows = $groupTable->get()->getResultArray();
        foreach ($groupRows as $row) {
            $groupIds[$row['slug']] = (int) $row['id'];
        }

        $purposeTable = $this->db->table('booking_purposes');
        $purposes = $purposeTable->get()->getResultArray();
        $hasPurposeGroup = $this->db->fieldExists('purpose_group', 'booking_purposes');

        $nameMap = [
            'Shooting' => 'production',
            'Recording' => 'production',
            'Live Broadcast' => 'production',
            'Program Production' => 'production',
            'News Coverage' => 'production',
            'OB Production' => 'production',
            'Radio Program' => 'audio',
            'Podcast Recording' => 'audio',
            'Voice Recording' => 'audio',
            'Dubbing' => 'audio',
            'Field Recording' => 'audio',
            'Setup / Preparation' => 'preparation',
            'Technical Testing' => 'preparation',
            'Rehearsal' => 'preparation',
            'Equipment Setup' => 'preparation',
            'Training Session' => 'training',
            'Workshop' => 'training',
            'Seminar' => 'training',
            'Lecture' => 'training',
            'Internal Meeting' => 'meetings-events',
            'Meeting' => 'meetings-events',
            'Presentation' => 'meetings-events',
            'Event' => 'meetings-events',
            'Press Conference' => 'meetings-events',
            'Outdoor Shooting' => 'field-operations',
            'Maintenance' => 'maintenance',
            'Repair' => 'maintenance',
            'Inspection' => 'maintenance',
            'Other' => 'other',
        ];

        $legacyGroupMap = [
            'Production' => 'production',
            'Audio' => 'audio',
            'Preparation' => 'preparation',
            'Training' => 'training',
            'Meetings & Events' => 'meetings-events',
            'Field Operations' => 'field-operations',
            'Maintenance' => 'maintenance',
            'Other' => 'other',
            'General' => 'other',
        ];

        foreach ($purposes as $purpose) {
            $slug = $nameMap[$purpose['name']] ?? 'other';

            if ($hasPurposeGroup && ! empty($purpose['purpose_group'])) {
                $slug = $legacyGroupMap[$purpose['purpose_group']] ?? $slug;
            }

            $purposeTable->where('id', $purpose['id'])->update([
                'group_id' => $groupIds[$slug] ?? $groupIds['other'],
            ]);
        }

        try {
            $this->db->query('ALTER TABLE `booking_purposes` ADD INDEX `idx_booking_purposes_group_id` (`group_id`)');
        } catch (\Throwable $e) {
        }

        try {
            $this->db->query('ALTER TABLE `booking_purposes` ADD CONSTRAINT `fk_booking_purposes_group_id` FOREIGN KEY (`group_id`) REFERENCES `booking_purpose_groups` (`id`) ON DELETE SET NULL ON UPDATE CASCADE');
        } catch (\Throwable $e) {
        }

        if ($this->db->fieldExists('purpose_group', 'booking_purposes')) {
            try {
                $this->db->query('ALTER TABLE `booking_purposes` DROP INDEX `idx_booking_purposes_group`');
            } catch (\Throwable $e) {
            }

            $this->forge->dropColumn('booking_purposes', 'purpose_group');
        }
    }

    public function down()
    {
        if (! $this->db->fieldExists('purpose_group', 'booking_purposes')) {
            $this->forge->addColumn('booking_purposes', [
                'purpose_group' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                    'after'      => 'name',
                ],
            ]);
        }

        try {
            $this->db->query(
                'UPDATE `booking_purposes` bp
                LEFT JOIN `booking_purpose_groups` bpg ON bpg.id = bp.group_id
                SET bp.purpose_group = bpg.name
                WHERE bp.group_id IS NOT NULL'
            );
        } catch (\Throwable $e) {
        }

        try {
            $this->db->query('ALTER TABLE `booking_purposes` ADD INDEX `idx_booking_purposes_group` (`purpose_group`)');
        } catch (\Throwable $e) {
        }

        try {
            $this->db->query('ALTER TABLE `booking_purposes` DROP FOREIGN KEY `fk_booking_purposes_group_id`');
        } catch (\Throwable $e) {
        }

        try {
            $this->db->query('ALTER TABLE `booking_purposes` DROP INDEX `idx_booking_purposes_group_id`');
        } catch (\Throwable $e) {
        }

        if ($this->db->fieldExists('group_id', 'booking_purposes')) {
            $this->forge->dropColumn('booking_purposes', 'group_id');
        }

        if ($this->db->tableExists('booking_purpose_groups')) {
            $this->forge->dropTable('booking_purpose_groups');
        }
    }
}
