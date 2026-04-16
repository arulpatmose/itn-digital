<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Dashboard::index', ['as' => 'dashboard']);

/*
| --------------------------------------------------------------------
| User Management Routes
| --------------------------------------------------------------------
*/
// Profile routes — any authenticated user
$routes->group('users', ['filter' => 'session'], function ($routes) {
    $routes->get('profile', 'Users::profile');
    $routes->post('update-profile', 'Users::updateProfile');
    $routes->post('update-password', 'Users::changePassword');
});

// User management routes — require users.manage-admins
$routes->group('users', ['filter' => 'permission:users.manage-admins'], function ($routes) {
    $routes->get('/', 'Users::index', ['as' => 'users']);
    $routes->get('add', 'Users::create', ['filter' => 'permission:users.create']);
    $routes->post('submit', 'Users::store', ['filter' => 'permission:users.create']);
    $routes->post('update-user', 'Users::updateUser', ['filter' => 'permission:users.edit']);
    $routes->post('update-user-password', 'Users::changeUserPassword', ['filter' => 'permission:users.edit']);
    $routes->post('update-user-groups', 'Users::updateUserGroups', ['filter' => 'permission:users.manage-admins']);
    $routes->get('edit/(:num)', 'Users::edit/$1', ['filter' => 'permission:users.edit']);
    $routes->post('delete', 'Users::deleteUser', ['filter' => 'permission:users.delete']);
    $routes->post('restore', 'Users::restoreUser', ['filter' => 'permission:users.restore']);
    $routes->post('ban', 'Users::banUser', ['filter' => 'permission:users.ban']);
    $routes->post('unban', 'Users::unBanUser', ['filter' => 'permission:users.unban']);
});

/*
| --------------------------------------------------------------------
| Client Management Routes
| --------------------------------------------------------------------
*/
$routes->group('clients', ['filter' => 'permission:clients.access'], function ($routes) {
    $routes->get('/', 'Clients::index', ['as' => 'clients']);
    $routes->get('add', 'Clients::create', ['filter' => 'permission:clients.create']);
    $routes->post('submit', 'Clients::store', ['filter' => 'permission:clients.create']);
    $routes->get('edit/(:num)', 'Clients::edit/$1', ['filter' => 'permission:clients.edit']);
    $routes->post('update/(:num)', 'Clients::update/$1', ['filter' => 'permission:clients.edit']);
    $routes->post('delete', 'Clients::destroy', ['filter' => 'permission:clients.delete']);
});

/*
| --------------------------------------------------------------------
| Schedule Routes (Bulk)
| --------------------------------------------------------------------
*/
$routes->group('schedules', ['filter' => 'permission:schedules.access'], function ($routes) {
    $routes->get('/', 'Schedules::index', ['as' => 'schedules']);
    $routes->get('add', 'Schedules::create', ['filter' => 'permission:schedules.create']);
    $routes->post('submit', 'Schedules::store', ['filter' => 'permission:schedules.create']);
    $routes->get('edit/(:num)', 'Schedules::edit/$1', ['filter' => 'permission:schedules.edit']);
    $routes->post('update/(:num)', 'Schedules::update/$1', ['filter' => 'permission:schedules.edit']);
    $routes->post('delete', 'Schedules::destroy', ['filter' => 'permission:schedules.delete']);
    $routes->post('fetch-comments', 'Schedules::fetchComments', ['filter' => 'permission:schedules.access']);
});

/*
| --------------------------------------------------------------------
| Individual Schedule Routes
| --------------------------------------------------------------------
*/
$routes->group('schedule', ['filter' => 'permission:schedule.access'], function ($routes) {
    $routes->get('(:num)', 'Schedule::index/$1');
    $routes->get('add/(:num)', 'Schedule::create/$1', ['filter' => 'permission:schedule.create']);
    $routes->post('submit/(:num)', 'Schedule::store/$1', ['filter' => 'permission:schedule.create']);
    $routes->post('delete', 'Schedule::destroy', ['filter' => 'permission:schedule.delete']);
});

/*
| --------------------------------------------------------------------
| Programs
| --------------------------------------------------------------------
*/
$routes->group('programs', ['filter' => 'permission:programs.access'], function ($routes) {
    $routes->get('/', 'Programs::index', ['as' => 'programs']);
    $routes->get('add', 'Programs::create', ['filter' => 'permission:programs.create']);
    $routes->post('submit', 'Programs::store', ['filter' => 'permission:programs.create']);
    $routes->get('edit/(:num)', 'Programs::edit/$1', ['filter' => 'permission:programs.edit']);
    $routes->post('update/(:num)', 'Programs::update/$1', ['filter' => 'permission:programs.edit']);
    $routes->post('delete', 'Programs::destroy', ['filter' => 'permission:programs.delete']);
});

/*
| --------------------------------------------------------------------
| Spots
| --------------------------------------------------------------------
*/
$routes->group('spots', ['filter' => 'permission:spots.access'], function ($routes) {
    $routes->get('/', 'Spots::index', ['as' => 'spots']);
    $routes->get('add', 'Spots::create', ['filter' => 'permission:spots.create']);
    $routes->post('submit', 'Spots::store', ['filter' => 'permission:spots.create']);
    $routes->get('edit/(:num)', 'Spots::edit/$1', ['filter' => 'permission:spots.edit']);
    $routes->post('update/(:num)', 'Spots::update/$1', ['filter' => 'permission:spots.edit']);
    $routes->post('delete', 'Spots::destroy', ['filter' => 'permission:spots.delete']);
});

/*
| --------------------------------------------------------------------
| Formats
| --------------------------------------------------------------------
*/
$routes->group('formats', ['filter' => 'permission:formats.access'], function ($routes) {
    $routes->get('/', 'Formats::index', ['as' => 'formats']);
    $routes->get('add', 'Formats::create', ['filter' => 'permission:formats.create']);
    $routes->post('submit', 'Formats::store', ['filter' => 'permission:formats.create']);
    $routes->get('edit/(:num)', 'Formats::edit/$1', ['filter' => 'permission:formats.edit']);
    $routes->post('update/(:num)', 'Formats::update/$1', ['filter' => 'permission:formats.edit']);
    $routes->post('delete', 'Formats::destroy', ['filter' => 'permission:formats.delete']);
});

/*
| --------------------------------------------------------------------
| Platforms
| --------------------------------------------------------------------
*/
$routes->group('platforms', ['filter' => 'permission:platforms.access'], function ($routes) {
    $routes->get('/', 'Platforms::index', ['as' => 'platforms']);
    $routes->get('add', 'Platforms::create', ['filter' => 'permission:platforms.create']);
    $routes->post('submit', 'Platforms::store', ['filter' => 'permission:platforms.create']);
    $routes->get('edit/(:num)', 'Platforms::edit/$1', ['filter' => 'permission:platforms.edit']);
    $routes->post('update/(:num)', 'Platforms::update/$1', ['filter' => 'permission:platforms.edit']);
    $routes->post('delete', 'Platforms::destroy', ['filter' => 'permission:platforms.delete']);
});

/*
| --------------------------------------------------------------------
| Commercials
| --------------------------------------------------------------------
*/
$routes->group('commercials', ['filter' => 'permission:commercials.access'], function ($routes) {
    $routes->get('/', 'Commercials::index', ['as' => 'commercials']);
    $routes->get('add', 'Commercials::create', ['filter' => 'permission:commercials.create']);
    $routes->post('submit', 'Commercials::store', ['filter' => 'permission:commercials.create']);
    $routes->get('edit/(:num)', 'Commercials::edit/$1', ['filter' => 'permission:commercials.edit']);
    $routes->post('update/(:num)', 'Commercials::update/$1', ['filter' => 'permission:commercials.edit']);
    $routes->post('delete', 'Commercials::destroy', ['filter' => 'permission:commercials.delete']);
});

/*
| --------------------------------------------------------------------
| Daily Schedule
| --------------------------------------------------------------------
*/
$routes->group('daily-schedule', ['filter' => 'permission:daily_schedule.access'], function ($routes) {
    $routes->post('edit/(:num)', 'DailySchedule::edit/$1', ['filter' => 'permission:daily_schedule.edit']);
    $routes->post('update/(:num)', 'DailySchedule::update/$1', ['filter' => 'permission:daily_schedule.edit']);
    $routes->post('update-bulk', 'DailySchedule::updateBulk', ['filter' => 'permission:daily_schedule.edit']);
    $routes->post('fetch-comments', 'DailySchedule::fetchComments');
    $routes->get('/', 'DailySchedule::index/');
    $routes->get('(:segment)', 'DailySchedule::index/$1');
});

/*
| --------------------------------------------------------------------
| System Settings Routes
| --------------------------------------------------------------------
*/
$routes->group('settings', ['filter' => 'permission:admin.settings'], function ($routes) {
    $routes->get('/', 'Settings::index', ['filter' => 'permission:admin.settings']);
    $routes->get('(:segment)', 'Settings::index/$1', ['filter' => 'permission:admin.settings']);
    $routes->post('update', 'Settings::update', ['filter' => 'permission:admin.settings']);
    $routes->post('email-test', 'Settings::testEmail', ['filter' => 'permission:admin.settings']);
    $routes->get('forget/(:segment)/(:segment)', 'Settings::forget/$1/$2', ['filter' => 'permission:admin.settings']);
});

/*
| --------------------------------------------------------------------
| Accounts
| --------------------------------------------------------------------
*/
$routes->get('accounts', 'Accounts::index', ['as' => 'accounts']);

/*
| --------------------------------------------------------------------
| Migration Runner Routes
| --------------------------------------------------------------------
*/
$routes->group('migrations', ['filter' => 'permission:admin.migrations'], function ($routes) {
    $routes->get('/', 'MigrationRunner::index', ['as' => 'migrations']);
    $routes->post('run', 'MigrationRunner::run');
    $routes->post('run-single', 'MigrationRunner::runSingle');
    $routes->post('sync', 'MigrationRunner::sync');
    $routes->post('rollback/(:num)', 'MigrationRunner::rollback/$1');
});

/*
| --------------------------------------------------------------------
| Activity Log Routes
| --------------------------------------------------------------------
*/
$routes->group('activity-log', ['filter' => 'permission:admin.settings'], function ($routes) {
    $routes->get('/', 'ActivityLog::index', ['as' => 'activity_log']);
});

/*
| --------------------------------------------------------------------
| Bookings Routes
| --------------------------------------------------------------------
*/
$routes->group('bookings', ['filter' => 'permission:booking.access'], function ($routes) {
    $routes->get('/', 'Bookings::index', ['as' => 'bookings', 'filter' => 'permission:booking.approve']);
    $routes->get('my-bookings', 'Bookings::myBookings', ['as' => 'my_bookings']);
    $routes->get('create', 'Bookings::create', ['filter' => 'permission:booking.create']);
    $routes->post('submit', 'Bookings::store', ['filter' => 'permission:booking.create']);
    $routes->post('approve', 'Bookings::approve', ['filter' => 'permission:booking.approve']);
    $routes->post('reject', 'Bookings::reject', ['filter' => 'permission:booking.approve']);
    $routes->post('cancel', 'Bookings::cancel', ['filter' => 'permission:booking.cancel']);
    $routes->post('available-slots', 'Bookings::availableSlots');
    $routes->get('calendar', 'Bookings::calendar');
    $routes->get('calendar-events', 'Bookings::calendarEvents');
});

/*
| --------------------------------------------------------------------
| Resources Routes
| --------------------------------------------------------------------
*/
$routes->group('resources', ['filter' => 'permission:resource.access'], function ($routes) {
    $routes->get('/', 'Resources::index', ['as' => 'resources']);
    $routes->get('add', 'Resources::create', ['filter' => 'permission:resource.create']);
    $routes->post('submit', 'Resources::store', ['filter' => 'permission:resource.create']);
    $routes->get('edit/(:num)', 'Resources::edit/$1', ['filter' => 'permission:resource.edit']);
    $routes->post('update/(:num)', 'Resources::update/$1', ['filter' => 'permission:resource.edit']);
    $routes->post('delete', 'Resources::destroy', ['filter' => 'permission:resource.delete']);
    $routes->post('toggle-status', 'Resources::toggleStatus', ['filter' => 'permission:resource.edit']);
});

/*
| --------------------------------------------------------------------
| Resource Types Routes
| --------------------------------------------------------------------
*/
$routes->group('resource-types', ['filter' => 'permission:resource_type.access'], function ($routes) {
    $routes->get('/', 'ResourceTypes::index', ['as' => 'resource_types']);
    $routes->post('submit', 'ResourceTypes::store', ['filter' => 'permission:resource_type.create']);
    $routes->post('update/(:num)', 'ResourceTypes::update/$1', ['filter' => 'permission:resource_type.edit']);
    $routes->post('delete', 'ResourceTypes::destroy', ['filter' => 'permission:resource_type.delete']);
});

/*
| --------------------------------------------------------------------
| Booking Purpose Group Routes
| --------------------------------------------------------------------
*/
$routes->group('booking-purpose-groups', ['filter' => 'permission:booking_purpose_group.access'], function ($routes) {
    $routes->get('/', 'BookingPurposeGroups::index', ['as' => 'booking_purpose_groups']);
    $routes->post('submit', 'BookingPurposeGroups::store', ['filter' => 'permission:booking_purpose_group.create']);
    $routes->post('update/(:num)', 'BookingPurposeGroups::update/$1', ['filter' => 'permission:booking_purpose_group.edit']);
    $routes->post('toggle-status', 'BookingPurposeGroups::toggleStatus', ['filter' => 'permission:booking_purpose_group.edit']);
    $routes->post('delete', 'BookingPurposeGroups::destroy', ['filter' => 'permission:booking_purpose_group.delete']);
});

/*
| --------------------------------------------------------------------
| Booking Purpose Routes
| --------------------------------------------------------------------
*/
$routes->group('booking-purposes', ['filter' => 'permission:booking_purpose.access'], function ($routes) {
    $routes->get('/', 'BookingPurposes::index', ['as' => 'booking_purposes']);
    $routes->post('submit', 'BookingPurposes::store', ['filter' => 'permission:booking_purpose.create']);
    $routes->post('update/(:num)', 'BookingPurposes::update/$1', ['filter' => 'permission:booking_purpose.edit']);
    $routes->post('toggle-status', 'BookingPurposes::toggleStatus', ['filter' => 'permission:booking_purpose.edit']);
    $routes->post('delete', 'BookingPurposes::destroy', ['filter' => 'permission:booking_purpose.delete']);
});

/*
| --------------------------------------------------------------------
| API Service Routes
| --------------------------------------------------------------------
*/
$routes->group('api', function ($routes) {
    $routes->post('get-all-programs', 'APIServices::getAllPrograms');
    $routes->post('get-all-spots', 'APIServices::getAllSpots');
    $routes->post('get-all-formats', 'APIServices::getAllFormats');
    $routes->post('get-all-clients', 'APIServices::getAllClients');
    $routes->post('get-all-platforms', 'APIServices::getAllPlatforms');
    $routes->post('get-all-commercials', 'APIServices::getAllCommercials');
    $routes->post('get-select-options', 'APIServices::getSelectOptions');
    $routes->post('get-all-users', 'APIServices::getAllUsers');
    $routes->post('get-all-schedules', 'APIServices::getAllSchedules');
    $routes->post('get-schedules-budget', 'APIServices::getAllScheduleForAccounts');
    $routes->post('get-all-activity-logs', 'APIServices::getAllActivityLogs');
});

service('auth')->routes($routes);
