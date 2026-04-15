<?php

declare(strict_types=1);

/**
 * This file is part of CodeIgniter Shield.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Config;

use CodeIgniter\Shield\Config\AuthGroups as ShieldAuthGroups;

class AuthGroups extends ShieldAuthGroups
{
    /**
     * --------------------------------------------------------------------
     * Default Group
     * --------------------------------------------------------------------
     * The group that a newly registered user is added to.
     */
    public string $defaultGroup = 'scheduler';

    /**
     * --------------------------------------------------------------------
     * Groups
     * --------------------------------------------------------------------
     * An associative array of the available groups in the system, where the keys
     * are the group names and the values are arrays of the group info.
     *
     * Whatever value you assign as the key will be used to refer to the group
     * when using functions such as:
     *      $user->addGroup('superadmin');
     *
     * @var array<string, array<string, string>>
     *
     * @see https://codeigniter4.github.io/shield/quick_start_guide/using_authorization/#change-available-groups for more info
     */
    public array $groups = [
        'superadmin' => [
            'title'       => 'Super Admin',
            'description' => 'Complete control of the site.',
        ],
        'admin' => [
            'title'       => 'Admin',
            'description' => 'Day to day administrators of the site.',
        ],
        'developer' => [
            'title'       => 'Developer',
            'description' => 'Site programmers.',
        ],
        'user' => [
            'title'       => 'User',
            'description' => 'General users of the site. Often customers.',
        ],
        'beta' => [
            'title'       => 'Beta User',
            'description' => 'Has access to beta-level features.',
        ],
        'scheduler' => [
            'title'       => 'Scheduling Officer',
            'description' => 'Commercial Scheduling Officers',
        ],
        'webeditor' => [
            'title'       => 'Content Creator',
            'description' => 'Creates and manages digital contents',
        ],
        'marketing_executive' => [
            'title'       => 'Marketing Executive',
            'description' => 'Drive profit and promote products and services for ITN Digital',
        ],
        'resource_manager' => [
            'title'       => 'Resource Manager',
            'description' => 'Dedicated admin for the booking system — manages resources, resource types, time slots, and approves or rejects booking requests.',
        ],
        'booking_user' => [
            'title'       => 'Booking User',
            'description' => 'General staff member who can request, view, and cancel their own resource bookings.',
        ],
        'secretary' => [
            'title'       => 'Secretary',
            'description' => 'Requests bookings on behalf of users or departments.',
        ]
    ];

    /**
     * --------------------------------------------------------------------
     * Permissions
     * --------------------------------------------------------------------
     * The available permissions in the system.
     *
     * If a permission is not listed here it cannot be used.
     */
    public array $permissions = [

        // 🔐 Admin Area & Core Access
        'admin.access'          => 'Can access the site\'s admin area',
        'admin.settings'        => 'Can access the main site settings',
        'admin.migrations'      => 'Can run and manage database migrations',
        'dashboard.access'      => 'Can access the dashboard',
        'beta.access'           => 'Can access beta-level features',

        // 👥 Admin & User Management
        'users.manage-admins'   => 'Can manage other admins',
        'users.create'          => 'Can create new non-admin users',
        'users.edit'            => 'Can edit existing non-admin users',
        'users.delete'          => 'Can delete existing non-admin users',
        'users.restore'         => 'Can restore soft-deleted users',
        'users.ban'             => 'Can ban users from the site',
        'users.unban'           => 'Can unban users from the site',

        // 🧑‍💼 Client Management
        'clients.access'        => 'Can access clients data',
        'clients.create'        => 'Can create new client',
        'clients.edit'          => 'Can edit existing client',
        'clients.delete'        => 'Can delete existing client',

        // 📺 Commercials Management
        'commercials.access'    => 'Can access commercials data',
        'commercials.create'    => 'Can create new commercial',
        'commercials.edit'      => 'Can edit existing commercial',
        'commercials.delete'    => 'Can delete existing commercial',

        // 📝 Formats Management
        'formats.access'        => 'Can access formats data',
        'formats.create'        => 'Can create new format',
        'formats.edit'          => 'Can edit existing format',
        'formats.delete'        => 'Can delete existing format',

        // 🌐 Platforms Management
        'platforms.access'      => 'Can access platforms data',
        'platforms.create'      => 'Can create new platform',
        'platforms.edit'        => 'Can edit existing platform',
        'platforms.delete'      => 'Can delete existing platform',

        // 📌 Spots Management
        'spots.access'          => 'Can access spots data',
        'spots.create'          => 'Can create new spot',
        'spots.edit'            => 'Can edit existing spot',
        'spots.delete'          => 'Can delete existing spot',

        // 🎞️ Programs Management
        'programs.access'       => 'Can access programs data',
        'programs.create'       => 'Can create new program',
        'programs.edit'         => 'Can edit existing program',
        'programs.delete'       => 'Can delete existing program',

        // 🗓️ Schedule Management
        'schedule.access'       => 'Can access schedule data',
        'schedule.create'       => 'Can create new schedule',
        'schedule.edit'         => 'Can edit existing schedule',
        'schedule.delete'       => 'Can delete existing schedule',

        // 🗓️ Schedules (Bulk/Multiple) Management
        'schedules.access'      => 'Can access schedules data',
        'schedules.create'      => 'Can create new schedule',
        'schedules.edit'        => 'Can edit existing schedule',
        'schedules.delete'      => 'Can delete existing schedule',

        // 📆 Daily Schedule Management
        'daily_schedule.access'  => 'Can access daily schedules data',
        'daily_schedule.edit'    => 'Can edit existing daily schedules',

        // 💳 Accounts Management
        'accounts.access'       => 'Can access accounts data',

        // 📌 Booking Operations
        'booking.access'    => 'Can view all bookings',
        'booking.create'        => 'Can create a new booking',
        'booking.edit'          => 'Can edit own booking requests',
        'booking.cancel'        => 'Can cancel own bookings',
        'booking.approve'       => 'Can approve or reject booking requests',
        'booking_purpose_group.access' => 'Can view all booking purpose groups',
        'booking_purpose_group.create' => 'Can create booking purpose groups',
        'booking_purpose_group.edit'   => 'Can edit booking purpose groups',
        'booking_purpose_group.delete' => 'Can delete booking purpose groups',
        'booking_purpose.access' => 'Can view all booking purposes',
        'booking_purpose.create' => 'Can create booking purposes',
        'booking_purpose.edit'   => 'Can edit booking purposes',
        'booking_purpose.delete' => 'Can delete booking purposes',

        // 📅 Schedule & Booking Management
        'schedule.access'       => 'Can access schedule data',
        'schedule.create'       => 'Can create new schedule',
        'schedule.edit'         => 'Can edit existing schedule',
        'schedule.delete'       => 'Can delete existing schedule',

        // 🏢 Resource Management
        'resource.access'       => 'Can view all resources',
        'resource.create'       => 'Can create new resources',
        'resource.edit'         => 'Can edit existing resources',
        'resource.delete'       => 'Can delete resources',

        // 🗂 Resource Types Management
        'resource_type.access'   => 'Can view all resource types',
        'resource_type.create'   => 'Can create new resource types',
        'resource_type.edit'     => 'Can edit resource types',
        'resource_type.delete'   => 'Can delete resource types',

        // ⏰ Time Slot Management
        'time_slot.access'       => 'Can view all time slots',
        'time_slot.create'       => 'Can create new time slots',
        'time_slot.edit'         => 'Can edit existing time slots',
        'time_slot.delete'       => 'Can delete time slots',
    ];

    /**
     * --------------------------------------------------------------------
     * Permissions Matrix
     * --------------------------------------------------------------------
     * Maps permissions to groups.
     *
     * This defines group-level permissions.
     */
    public array $matrix = [
        'superadmin' => [
            'admin.*',
            'dashboard.*',
            'users.*',
            'beta.*',
            'clients.*',
            'programs.*',
            'commercials.*',
            'formats.*',
            'platforms.*',
            'spots.*',
            'schedule.*',
            'schedules.*',
            'daily_schedule.*',
            'accounts.*',
            'resource.*',
            'resource_type.*',
            'time_slot.*',
            'booking_purpose_group.*',
            'booking_purpose.*',
            'booking.*',
        ],
        'admin' => [
            'admin.access',
            'dashboard.*',
            'users.manage-admins',
            'users.create',
            'users.edit',
            'users.delete',
            'users.restore',
            'users.ban',
            'users.unban',
            'beta.access',
            'schedule.*',
            'schedules.*',
            'programs.*',
            'daily_schedule.*',
            'resource.*',
            'resource_type.*',
            'time_slot.*',
            'booking_purpose_group.*',
            'booking_purpose.*',
            'booking.access',
            'booking.create',
            'booking.edit',
            'booking.cancel',
            'booking.approve',
        ],
        'developer' => [
            'admin.access',
            'admin.settings',
            'users.create',
            'users.edit',
            'beta.access',
        ],
        'webeditor' => [
            'dashboard.*',
            'daily_schedule.*',
            'clients.access',
            'programs.access',
            'commercials.access',
            'formats.access',
            'platforms.access',
            'spots.access',
            'schedule.access',
            'schedule.create',
            'schedule.edit',
            'schedules.access',
            'schedules.create',
            'schedules.edit',
        ],
        'user' => [],
        'beta' => [
            'beta.access',
        ],
        'scheduler' => [
            'dashboard.*',
            'daily_schedule.access',
            'clients.access',
            'clients.create',
            'clients.edit',
            'programs.access',
            'commercials.access',
            'commercials.create',
            'commercials.edit',
            'formats.access',
            'platforms.access',
            'spots.access',
            'schedule.access',
            'schedule.create',
            'schedule.edit',
            'schedule.delete',
            'schedules.access',
            'schedules.create',
            'schedules.edit',
            'schedules.delete',
        ],
        'accountant' => [
            'dashboard.*',
            'accounts.*'
        ],
        'marketingex' => [
            'dashboard.*',
            'commercials.access',
            'clients.access',
            'schedules.access',
            'schedule.access',
        ],
        'resource_manager' => [
            'dashboard.*',
            // Resource management
            'resource.*',
            'resource_type.*',
            'time_slot.*',
            'booking_purpose_group.*',
            'booking_purpose.*',
            // Full booking oversight
            'booking.access',
            'booking.approve',
            'booking.cancel',  // can cancel any booking
            'booking.edit',
        ],
        'booking_user' => [
            'dashboard.*',
            // Can request and manage own bookings only
            'booking.access',
            'booking.create',
            'booking.edit',
            'booking.cancel',
        ],
        'secretary' => [
            'dashboard.*',
            'booking.create',
            'booking.access',
            'booking.edit',
            'booking.cancel',
        ],
    ];
}
