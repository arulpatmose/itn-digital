<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Template extends BaseConfig
{
    // **************************************************************************************************
    // GLOBAL META & OPEN GRAPH DATA
    // **************************************************************************************************

    // : The data is added in the <head> section of the page

    public $author = 'Arul Patmose Parmanathan';
    public $robots = 'noindex, nofollow';
    public $description = 'ITN Digital Portal is a gateway and digital portals provide links and directions to multiple sources of internal information at ITN';
    public $site_title = "ITN Digital Portal";

    // **************************************************************************************************
    // GLOBAL GENERIC
    // **************************************************************************************************
    // ''                            : default color theme
    // 'amethyst'                    : Amethyst color theme
    // 'city'                        : City color theme
    // 'flat'                        : Flat color theme
    // 'modern'                      : Modern color theme
    // 'smooth'                      : Smooth color theme

    public $theme = 'modern';
    // true                          : Remembers active color theme and dark mode between pages using
    //                                 localStorage when set through
    //                                 - Theme helper buttons [data-toggle="theme"],
    //                                 - Layout helper buttons [data-toggle="layout" data-action="dark_mode_[on/off/toggle]"]
    //                                 - ..and/or One.layout('dark_mode_[on/off/toggle]')
    // false                         : Does not remember the active color theme and Dark Mode
    public $remember_theme             = true;

    // true                          : Enables Page Loader screen
    // false                         : Disables Page Loader screen
    public $page_loader                = true;

    // **************************************************************************************************
    // GLOBAL SIDEBAR & SIDE OVERLAY
    // **************************************************************************************************

    // true                          : Left Sidebar and right Side Overlay
    // false                         : Right Sidebar and left Side Overlay
    public $l_sidebar_left             = true;

    // true                          : Mini hoverable Sidebar (screen width > 991px)
    // false                         : Normal mode
    public $l_sidebar_mini             = false;

    // true                          : Visible Sidebar (screen width > 991px)
    // false                         : Hidden Sidebar (screen width > 991px)
    public $l_sidebar_visible_desktop  = true;

    // true                          : Visible Sidebar (screen width < 992px)
    // false                         : Hidden Sidebar (screen width < 992px)

    public $l_sidebar_visible_mobile   = false;

    // true                          : Dark themed Sidebar
    // false                         : Light themed Sidebar (works with Dark Mode off)
    public $l_sidebar_dark             = false;

    // true                          : Hoverable Side Overlay (screen width > 991px)
    // false                         : Normal mode
    public $l_side_overlay_hoverable   = false;

    // true                          : Visible Side Overlay
    // false                         : Hidden Side Overlay
    public $l_side_overlay_visible     = false;

    // true                          : Enables a visible clickable (closes Side Overlay) Page Overlay when Side Overlay opens
    // false                         : Disables Page Overlay when Side Overlay opens
    public $l_page_overlay             = true;

    // true                          : Custom scrolling (screen width > 991px)
    // false                         : Native scrolling
    public $l_side_scroll              = true;

    // **************************************************************************************************
    // GLOBAL HEADER
    // **************************************************************************************************
    // true                          : Fixed Header
    // false                         : Static Header
    public $l_header_fixed             = true;

    // true                          : Dark themed Header
    // false                         : Light themed Header (works with Dark Mode off)
    public $l_header_dark              = false;

    // **************************************************************************************************
    // GLOBAL DARK MODE
    // **************************************************************************************************
    // true                          : Dark Mode enabled
    // false                         : Dark Mode disabled
    public $l_dark_mode                = true;

    // **************************************************************************************************
    // GLOBAL INCLUDED VIEWS
    // **************************************************************************************************

    //                               : Useful for adding different sidebars/headers per page or per section

    public $inc_side_overlay           = '';
    public $inc_sidebar                = true;
    public $inc_header                 = true;
    public $inc_hero                   = true;
    public $inc_footer                 = true;

    // **************************************************************************************************
    // GLOBAL MAIN CONTENT
    // **************************************************************************************************
    // ''                            : Full width Main Content
    // 'boxed'                       : Full width Main Content with a specific maximum width (screen width > 1200px)
    // 'narrow'                      : Full width Main Content with a percentage width (screen width > 1200px)
    public $l_m_content                = '';

    // **************************************************************************************************
    // GLOBAL MAIN MENU
    // **************************************************************************************************

    // It will get compared with the url of each menu link to make the link active and set up main menu accordingly
    // If you are using query strings to load different pages, you can use the following value: basename($_SERVER['REQUEST_URI'])
    // public $main_nav_active            = basename($_SERVER['PHP_SELF']);

    // You can use the following array to create your main menu
    public $main_nav = array(
        array(
            'name'       => 'Dashboard',
            'icon'       => 'si si-speedometer',
            'url'        => '',
            'permission' => 'dashboard.access',
        ),
        array(
            'name'       => 'Schedules',
            'icon'       => 'si si-calendar',
            'permission' => 'schedules.access',
            'sub'        => array(
                array(
                    'name'       => 'All Schedules',
                    'url'        => 'schedules',
                    'permission' => 'schedules.access',
                ),
                array(
                    'name'       => 'Add Schedule',
                    'url'        => 'schedules/add',
                    'permission' => 'schedules.create',
                )
            )
        ),
        array(
            'name'       => 'Clients',
            'icon'       => 'si si-users',
            'permission' => 'clients.access',
            'sub'        => array(
                array(
                    'name'       => 'All Clients',
                    'url'        => 'clients',
                    'permission' => 'clients.access',
                ),
                array(
                    'name'       => 'Add Client',
                    'url'        => 'clients/add',
                    'permission' => 'clients.create',
                )
            )
        ),
        array(
            'name'       => 'Programs',
            'icon'       => 'fa fa-display',
            'permission' => 'programs.access',
            'sub'        => array(
                array(
                    'name'       => 'All Programs',
                    'url'        => 'programs',
                    'permission' => 'programs.access',
                ),
                array(
                    'name'       => 'Add Program',
                    'url'        => 'programs/add',
                    'permission' => 'programs.create',
                )
            )
        ),
        array(
            'name'       => 'Ad Spots',
            'icon'       => 'si si-target',
            'permission' => 'spots.access',
            'sub'        => array(
                array(
                    'name'       => 'All Spots',
                    'url'        => 'spots',
                    'permission' => 'spots.access',
                ),
                array(
                    'name'       => 'Add Spot',
                    'url'        => 'spots/add',
                    'permission' => 'spots.create',
                )
            )
        ),
        array(
            'name'       => 'Ad Formats',
            'icon'       => 'si si-list',
            'permission' => 'formats.access',
            'sub'        => array(
                array(
                    'name'       => 'All Formats',
                    'url'        => 'formats',
                    'permission' => 'formats.access',
                ),
                array(
                    'name'       => 'Add Format',
                    'url'        => 'formats/add',
                    'permission' => 'formats.create',
                )
            )
        ),
        array(
            'name'       => 'Platforms',
            'icon'       => 'si si-social-youtube',
            'permission' => 'platforms.access',
            'sub'        => array(
                array(
                    'name'       => 'All Platforms',
                    'url'        => 'platforms',
                    'permission' => 'platforms.access',
                ),
                array(
                    'name'       => 'Add Platform',
                    'url'        => 'platforms/add',
                    'permission' => 'platforms.create',
                )
            )
        ),
        array(
            'name'       => 'Ads',
            'icon'       => 'fas fa-ad',
            'permission' => 'commercials.access',
            'sub'        => array(
                array(
                    'name'       => 'All Ads',
                    'url'        => 'commercials',
                    'permission' => 'commercials.access',
                ),
                array(
                    'name'       => 'Add Ad',
                    'url'        => 'commercials/add',
                    'permission' => 'commercials.create',
                )
            )
        ),
        array(
            'name'       => 'Settings',
            'icon'       => 'fa fa-cog',
            'url'        => 'settings',
            'permission' => 'settings.access',
        ),
        array(
            'name'       => 'Users',
            'icon'       => 'si si-user',
            'permission' => 'users.manage-admins',
            'sub'        => array(
                array(
                    'name'       => 'All Users',
                    'url'        => 'users',
                    'permission' => 'users.manage-admins',
                ),
                array(
                    'name'       => 'Add User',
                    'url'        => 'users/add',
                    'permission' => 'users.create',
                )
            )
        )
    );
}
