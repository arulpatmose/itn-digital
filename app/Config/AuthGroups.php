<?php

declare(strict_types=1);

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
     * @see https://github.com/codeigniter4/shield/blob/develop/docs/quickstart.md#change-available-groups for more info
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
        'marketingex' => [
            'title'       => 'Marketing Executive',
            'description' => 'Drive profit and promote products and services for ITN Digital',
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
        'admin.access'          => 'Can access the sites admin area',
        'admin.settings'        => 'Can access the main site settings',
        'users.manage-admins'   => 'Can manage other admins',
        'users.create'          => 'Can create new non-admin users',
        'users.edit'            => 'Can edit existing non-admin users',
        'users.delete'          => 'Can delete existing non-admin users',
        'beta.access'           => 'Can access beta-level features',
        'clients.access'        => 'Can access clients data',
        'clients.create'        => 'Can create new client',
        'clients.edit'          => 'Can edit existing client',
        'clients.delete'        => 'Can delete existing client',
        'commercials.access'    => 'Can access commercials data',
        'commercials.create'    => 'Can create new commercial',
        'commercials.edit'      => 'Can edit existing commercial',
        'commercials.delete'    => 'Can delete existing commercial',
        'formats.access'        => 'Can access formats data',
        'formats.create'        => 'Can create new format',
        'formats.edit'          => 'Can edit existing format',
        'formats.delete'        => 'Can delete existing format',
        'platforms.access'      => 'Can access platforms data',
        'platforms.create'      => 'Can create new platform',
        'platforms.edit'        => 'Can edit existing platform',
        'platforms.delete'      => 'Can delete existing platform',
        'spots.access'          => 'Can access spots data',
        'spots.create'          => 'Can create new spot',
        'spots.edit'            => 'Can edit existing spot',
        'spots.delete'          => 'Can delete existing spot',
        'programs.access'       => 'Can access programs data',
        'programs.create'       => 'Can create new program',
        'programs.edit'         => 'Can edit existing program',
        'programs.delete'       => 'Can delete existing program',
        'schedule.access'       => 'Can access schedule data',
        'schedule.create'       => 'Can create new schedule',
        'schedule.edit'         => 'Can edit existing schedule',
        'schedule.delete'       => 'Can delete existing schedule',
        'schedules.access'      => 'Can access schedules data',
        'schedules.create'      => 'Can create new schedule',
        'schedules.edit'        => 'Can edit existing schedule',
        'schedules.delete'      => 'Can delete existing schedule',
        'dailyschedule.access'  => 'Can access daily schedules data',
        'dailyschedule.edit'    => 'Can edit existing daily schedules',
        'accounts.access'       => 'Can access accounts data',
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
            'dailyschedule.*',
            'accounts.*'
        ],
        'admin' => [
            'admin.access',
            'users.create',
            'users.edit',
            'users.delete',
            'beta.access',
            'schedule.*',
            'schedules.*',
            'programs.*',
            'dailyschedule.*'
        ],
        'developer' => [
            'admin.access',
            'admin.settings',
            'users.create',
            'users.edit',
            'beta.access',
        ],
        'webeditor' => [
            'dailyschedule.*',
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
            'dailyschedule.access',
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
            'schedules.access',
            'schedules.create',
            'schedules.edit'
        ],
        'accountant' => [
            'accounts.*'
        ],
        'marketingex' => [
            'commercials.access',
            'clients.access',
            'schedules.access',
            'schedule.access',
        ]
    ];
}
