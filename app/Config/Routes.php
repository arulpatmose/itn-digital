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
$routes->group('users', function ($routes) {
    $routes->get('/', 'Users::index', ['as' => 'users']);
    $routes->get('add', 'Users::create');
    $routes->post('submit', 'Users::store');
    $routes->post('update-profile', 'Users::updateProfile');
    $routes->post('update-user', 'Users::updateUser');
    $routes->post('update-password', 'Users::changePassword');
    $routes->post('update-user-password', 'Users::changeUserPassword');
    $routes->post('update-user-groups', 'Users::updateUserGroups');
    $routes->get('profile', 'Users::profile');
    $routes->get('edit/(:num)', 'Users::edit/$1');
    $routes->post('delete', 'Users::deleteUser');
});

/*
| --------------------------------------------------------------------
| Client Management Routes
| --------------------------------------------------------------------
*/
$routes->group('clients', function ($routes) {
    $routes->get('/', 'Clients::index', ['as' => 'clients']);
    $routes->get('add', 'Clients::create');
    $routes->post('submit', 'Clients::store');
    $routes->get('edit/(:num)', 'Clients::edit/$1');
    $routes->post('update/(:num)', 'Clients::update/$1');
    $routes->post('delete', 'Clients::destroy');
});

/*
| --------------------------------------------------------------------
| Schedule Routes (Bulk & Individual)
| --------------------------------------------------------------------
*/
$routes->group('schedules', function ($routes) {
    $routes->get('/', 'Schedules::index', ['as' => 'schedules']);
    $routes->get('add', 'Schedules::create');
    $routes->post('submit', 'Schedules::store');
    $routes->get('edit/(:num)', 'Schedules::edit/$1');
    $routes->post('update/(:num)', 'Schedules::update/$1');
    $routes->post('delete', 'Schedules::destroy');
    $routes->post('fetch-comments', 'Schedules::fetchComments');
});

$routes->group('schedule', function ($routes) {
    $routes->get('(:num)', 'Schedule::index/$1');
    $routes->get('add/(:num)', 'Schedule::create/$1');
    $routes->post('submit/(:num)', 'Schedule::store/$1');
    $routes->post('delete', 'Schedule::destroy');
});

/*
| --------------------------------------------------------------------
| Program Routes
| --------------------------------------------------------------------
*/
$routes->group('programs', function ($routes) {
    $routes->get('/', 'Programs::index', ['as' => 'programs']);
    $routes->get('add', 'Programs::create');
    $routes->post('submit', 'Programs::store');
    $routes->get('edit/(:num)', 'Programs::edit/$1');
    $routes->post('update/(:num)', 'Programs::update/$1');
    $routes->post('delete', 'Programs::destroy');
});

/*
| --------------------------------------------------------------------
| Spot Routes
| --------------------------------------------------------------------
*/
$routes->group('spots', function ($routes) {
    $routes->get('/', 'Spots::index', ['as' => 'spots']);
    $routes->get('add', 'Spots::create');
    $routes->post('submit', 'Spots::store');
    $routes->get('edit/(:num)', 'Spots::edit/$1');
    $routes->post('update/(:num)', 'Spots::update/$1');
    $routes->post('delete', 'Spots::destroy');
});

/*
| --------------------------------------------------------------------
| Format Routes
| --------------------------------------------------------------------
*/
$routes->group('formats', function ($routes) {
    $routes->get('/', 'Formats::index', ['as' => 'formats']);
    $routes->get('add', 'Formats::create');
    $routes->post('submit', 'Formats::store');
    $routes->get('edit/(:num)', 'Formats::edit/$1');
    $routes->post('update/(:num)', 'Formats::update/$1');
    $routes->post('delete', 'Formats::destroy');
});

/*
| --------------------------------------------------------------------
| Platform Routes
| --------------------------------------------------------------------
*/
$routes->group('platforms', function ($routes) {
    $routes->get('/', 'Platforms::index', ['as' => 'platforms']);
    $routes->get('add', 'Platforms::create');
    $routes->post('submit', 'Platforms::store');
    $routes->get('edit/(:num)', 'Platforms::edit/$1');
    $routes->post('update/(:num)', 'Platforms::update/$1');
    $routes->post('delete', 'Platforms::destroy');
});

/*
| --------------------------------------------------------------------
| Commercial (Ads) Routes
| --------------------------------------------------------------------
*/
$routes->group('commercials', function ($routes) {
    $routes->get('/', 'Commercials::index', ['as' => 'commercials']);
    $routes->get('add', 'Commercials::create');
    $routes->post('submit', 'Commercials::store');
    $routes->get('edit/(:num)', 'Commercials::edit/$1');
    $routes->post('update/(:num)', 'Commercials::update/$1');
    $routes->post('delete', 'Commercials::destroy');
});

/*
| --------------------------------------------------------------------
| Daily Schedule Routes
| --------------------------------------------------------------------
*/
$routes->group('daily-schedule', function ($routes) {
    $routes->post('edit/(:num)', 'DailySchedule::edit/$1');
    $routes->post('update/(:num)', 'DailySchedule::update/$1');
    $routes->post('update-bulk', 'DailySchedule::updateBulk');
    $routes->post('fetch-comments', 'DailySchedule::fetchComments');
    $routes->get('/', 'DailySchedule::index/');
    $routes->get('(:segment)', 'DailySchedule::index/$1');
});

/*
| --------------------------------------------------------------------
| System Settings Routes
| --------------------------------------------------------------------
*/
$routes->group('settings', function ($routes) {
    // Master index (shows cards)
    $routes->get('/', 'Settings::index');

    // Individual section routes
    $routes->get('system', 'Settings::system');

    // Common update route
    $routes->post('update', 'Settings::updateSettings');
});

/*
| --------------------------------------------------------------------
| Accounts
| --------------------------------------------------------------------
*/
$routes->get('accounts', 'Accounts::index', ['as' => 'accounts']);

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
});

service('auth')->routes($routes);
