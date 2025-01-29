<?php

namespace Config;

use App\Controllers\Programs;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Dashboard');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Dashboard::index', ['as' => 'dashboard']);

// Users
$routes->get('users', 'Users::index', ['as' => 'users']);
$routes->get('users/add', 'Users::create');
$routes->post('users/submit', 'Users::store');
$routes->post('users/update-profile', 'Users::updateProfile');
$routes->post('users/update-user', 'Users::updateUser');
$routes->post('users/update-password', 'Users::changePassword');
$routes->post('users/update-user-password', 'Users::changeUserPassword');
$routes->post('users/update-user-groups', 'Users::updateUserGroups');
$routes->get('users/profile', 'Users::profile');
$routes->get('users/edit/(:num)', 'Users::edit/$1');
$routes->post('users/delete', 'Users::deleteUser');

// Schedule
$routes->get('schedules', 'Schedules::index', ['as' => 'schedules']);
$routes->get('schedules/add', 'Schedules::create');
$routes->post('schedules/submit', 'Schedules::store');
$routes->get('schedules/edit/(:num)', 'Schedules::edit/$1');
$routes->post('schedules/update/(:num)', 'Schedules::update/$1');
$routes->post('schedules/delete', 'Schedules::destroy');

// Single Schedule
$routes->get('schedule/(:num)', 'Schedule::index/$1');
$routes->get('schedule/add/(:num)', 'Schedule::create/$1');
$routes->post('schedule/submit/(:num)', 'Schedule::store/$1');
$routes->post('schedule/delete', 'Schedule::destroy');

// Programs
$routes->get('programs', 'Programs::index', ['as' => 'programs']);
$routes->get('programs/add', 'Programs::create');
$routes->post('programs/submit', 'Programs::store');
$routes->get('programs/edit/(:num)', 'Programs::edit/$1');
$routes->post('programs/update/(:num)', 'Programs::update/$1');
$routes->post('programs/delete', 'Programs::destroy');

// Spots
$routes->get('spots', 'Spots::index', ['as' => 'spots']);
$routes->get('spots/add', 'Spots::create');
$routes->post('spots/submit', 'Spots::store');
$routes->get('spots/edit/(:num)', 'Spots::edit/$1');
$routes->post('spots/update/(:num)', 'Spots::update/$1');
$routes->post('spots/delete', 'Spots::destroy');

// Formats
$routes->get('formats', 'Formats::index', ['as' => 'formats']);
$routes->get('formats/add', 'Formats::create');
$routes->post('formats/submit', 'Formats::store');
$routes->get('formats/edit/(:num)', 'Formats::edit/$1');
$routes->post('formats/update/(:num)', 'Formats::update/$1');
$routes->post('formats/delete', 'Formats::destroy');

// Clients
$routes->get('clients', 'Clients::index', ['as' => 'clients']);
$routes->get('clients/add', 'Clients::create');
$routes->post('clients/submit', 'Clients::store');
$routes->get('clients/edit/(:num)', 'Clients::edit/$1');
$routes->post('clients/update/(:num)', 'Clients::update/$1');
$routes->post('clients/delete', 'Clients::destroy');

// Platforms
$routes->get('platforms', 'Platforms::index', ['as' => 'platforms']);
$routes->get('platforms/add', 'Platforms::create');
$routes->post('platforms/submit', 'Platforms::store');
$routes->get('platforms/edit/(:num)', 'Platforms::edit/$1');
$routes->post('platforms/update/(:num)', 'Platforms::update/$1');
$routes->post('platforms/delete', 'Platforms::destroy');

// Commercials
$routes->get('commercials', 'Commercials::index', ['as' => 'commercials']);
$routes->get('commercials/add', 'Commercials::create');
$routes->post('commercials/submit', 'Commercials::store');
$routes->get('commercials/edit/(:num)', 'Commercials::edit/$1');
$routes->post('commercials/update/(:num)', 'Commercials::update/$1');
$routes->post('commercials/delete', 'Commercials::destroy');

// Daily Schedule

$routes->post('daily-schedule/edit/(:num)', 'DailySchedule::edit/$1');
$routes->post('daily-schedule/update/(:num)', 'DailySchedule::update/$1');
$routes->post('daily-schedule/update-bulk', 'DailySchedule::updateBulk');
$routes->get('daily-schedule(/:any)?', 'DailySchedule::index$1');

// Accounts
$routes->get('accounts', 'Accounts::index', ['as' => 'accounts']);

// API Services
$routes->post('api/get-all-programs', 'APIServices::getAllPrograms');
$routes->post('api/get-all-spots', 'APIServices::getAllSpots');
$routes->post('api/get-all-formats', 'APIServices::getAllFormats');
$routes->post('api/get-all-clients', 'APIServices::getAllClients');
$routes->post('api/get-all-platforms', 'APIServices::getAllPlatforms');
$routes->post('api/get-all-commercials', 'APIServices::getAllCommercials');
$routes->post('api/get-select-options', 'APIServices::getSelectOptions');
$routes->post('api/get-all-users', 'APIServices::getAllUsers');
$routes->post('api/get-all-schedules', 'APIServices::getAllSchedules');
$routes->post('api/get-schedules-budget', 'APIServices::getAllScheduleForAccounts');

service('auth')->routes($routes);

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
