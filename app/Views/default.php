<?php

$config = config('Template');

$main_nav_active = uri_string();

$data['config']['title'] = ($page_title ?? $config->site_title) . " | ITN Digital";
$data['config']['page_description'] = $page_description ?? $config->description;
$data['config']['og_url_site'] = base_url();
$data['config']['author'] = $config->author;
$data['config']['robots'] = $config->robots;
$data['config']['description'] = $config->description;
$data['config']['site_title'] = $config->site_title;
$data['config']['theme'] = $config->theme;
$data['config']['page_loader'] = $config->page_loader;

$data['config']['controller'] = $controller ?? null;
$data['config']['method'] = $controller ?? null;

if (isset($config->inc_header) && $config->inc_header) {
    $data['config']['inc_header'] = $config->inc_header;
}

if (isset($config->inc_hero) && $config->inc_hero) {
    $data['config']['inc_hero'] = $config->inc_hero;
}

if (isset($config->inc_side_overlay) && $config->inc_side_overlay) {
    $data['config']['inc_side_overlay'] = $config->inc_side_overlay;
}

if (isset($config->inc_sidebar) && $config->inc_sidebar) {
    $data['config']['inc_sidebar'] = $config->inc_sidebar;
}

if (isset($config->inc_footer) && $config->inc_footer) {
    $data['config']['inc_footer'] = $config->inc_footer;
}

$data['config']['html_classes'] = html_classes($config, false);
$data['config']['page_classes'] = page_classes($config, false);
$data['config']['main_nav'] = build_nav($main_nav_active, $config->main_nav, false, false);

echo view('default/master', $data);

/**
 * Builds <html> classes
 *
 * @param   boolean $print True to print the classes and False to return them
 *
 * @return  string  Returns the classes if $print is set to false
 */
function html_classes($config, $print = true)
{
    // Build <html> classes
    $html_classes  = '';

    if ($config->remember_theme) {
        $html_classes .= ' remember-theme';
    }

    // Print or return <html> classes
    if ($html_classes) {
        if ($print) {
            echo ' class="' . trim($html_classes) . '"';
        } else {
            return trim($html_classes);
        }
    } else {
        return false;
    }
}

/**
 * Builds #page-container classes
 *
 * @param   boolean $print True to print the classes and False to return them
 *
 * @return  string  Returns the classes if $print is set to false
 */
function page_classes($config, $print = true)
{
    // Build page classes

    $page_classes = '';

    if ($config->remember_theme) {
        $page_classes .= ' remember-theme';
    }

    // If sidebar is included
    if ($config->inc_sidebar) {
        if ($config->l_sidebar_visible_desktop) {
            $page_classes .= ' sidebar-o';
        }
        if ($config->l_sidebar_visible_mobile) {
            $page_classes .= ' sidebar-o-xs';
        }
        if ($config->l_sidebar_dark) {
            $page_classes .= ' sidebar-dark';
        }

        if ($config->l_sidebar_mini) {
            $page_classes .= ' sidebar-mini';
        }
    }

    // If side overlay is included
    if ($config->inc_side_overlay) {
        if ($config->l_side_overlay_hoverable) {
            $page_classes .= ' side-overlay-hover';
        }

        if ($config->l_side_overlay_visible) {
            $page_classes .= ' side-overlay-o';
        }

        if ($config->l_page_overlay) {
            $page_classes .= ' enable-page-overlay';
        }
    }

    // if sidebar or side overlay is included
    if ($config->inc_sidebar || $config->inc_side_overlay) {
        if (!$config->l_sidebar_left) {
            $page_classes .= ' sidebar-r';
        }

        if ($config->l_side_scroll) {
            $page_classes .= ' side-scroll';
        }
    }

    // If header is included
    if ($config->inc_header) {
        if ($config->l_header_fixed) {
            $page_classes .= ' page-header-fixed';
        }

        if ($config->l_header_dark) {
            $page_classes .= ' page-header-dark';
        }
    }

    // Main content classes
    if ($config->l_m_content == 'boxed') {
        $page_classes .= ' main-content-boxed';
    } else if ($config->l_m_content == 'narrow') {
        $page_classes .= ' main-content-narrow';
    }

    // Print or return page classes
    if ($page_classes) {
        if ($print) {
            echo ' class="' . trim($page_classes) . '"';
        } else {
            return trim($page_classes);
        }
    } else {
        return false;
    }
}

/**
 * Builds navigation
 *
 * @param   array       $nav_array A PHP array to create the menu from, False to use the default array
 * @param   boolean     $nav_horizontal True if the menu is for horizontal view as well
 * @param   boolean     $print True to print the navigation, False to return it
 *
 * @return  string      Returns the navigation if $print is set to false
 */
function build_nav($main_nav_active, $nav_array = false, $nav_horizontal = false, $print = true)
{
    // Clean navigation HTML
    $nav_html = '';

    $main_nav = array();

    // If a navigation array is not used, use the default one
    if (!$nav_array) {
        $nav_array = $main_nav;
    }

    // Build navigation
    $nav_html = build_nav_array($nav_array, $nav_horizontal, $main_nav_active);

    // Print or return navigation
    if ($print) {
        echo $nav_html;
    } else {
        return $nav_html;
    }
}

/**
 * Build navigation helper - Builds main navigation one level at a time
 *
 * @param   array       $nav_array A multi dimensional array with menu/sub menus links
 * @param   boolean     $nav_horizontal True if the menu is for horizontal view as well
 */
function build_nav_array($nav_array, $nav_horizontal, $main_nav_active)
{
    $nav_items = "";

    foreach ($nav_array as $node) {
        // Get all vital link info
        $link_name      = '<span class="nav-main-link-name">' . (isset($node['name']) ? $node['name'] : '') . '</span>' . "\n";
        $link_icon      = isset($node['icon']) ? '<i class="nav-main-link-icon ' . $node['icon'] . '"></i>' . "\n" : '';
        $link_badge     = isset($node['badge']) ? '<span class="nav-main-link-badge badge rounded-pill bg-' . (is_array($node['badge']) ? $node['badge'][1] : 'primary') . '">' . (is_array($node['badge']) ? $node['badge'][0] : $node['badge']) . '</span>' . "\n" : '';
        $link_url       = isset($node['url']) ? base_url($node['url']) : '#';
        $link_sub       = isset($node['sub']) && is_array($node['sub']) ? true : false;
        $link_type      = isset($node['type']) ? isset($node['type']) : '';
        $sub_active     = false;
        $link_active    = isset($node['url']) && $node['url'] == $main_nav_active ? true : false;

        // If link type is a header
        if ($link_type == 'heading') {
            $nav_items .= "<li class=\"nav-main-heading\">" . (isset($node['name']) ? $node['name'] : '') . "</li>\n";
        } else {
            // If it is a submenu search for an active link in all sub links
            if ($link_sub) {
                $sub_active = build_nav_array_search($node['sub'], $main_nav_active) ? true : false;
            }

            // Set menu properties
            $li_prop        = ' class="nav-main-item' . ($sub_active && !$nav_horizontal ? ' open' : '') . '"';
            $link_prop      = ' class="nav-main-link' . ($link_active || ($sub_active && $nav_horizontal)  ? ' active' : '') . ($link_sub ? ' nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="' . ($sub_active ? 'true' : 'false') . '"' : '"');

            // Add the link
            $nav_items .= "<li$li_prop>\n";

            $nav_items .= "<a$link_prop href=\"$link_url\">\n$link_icon$link_name$link_badge</a>\n";

            // If it is a submenu, call the function again
            if ($link_sub) {
                $nav_items .= "<ul class=\"nav-main-submenu\">\n";
                $nav_items .= build_sub_nav_array($node['sub'], $nav_horizontal, $main_nav_active);
                $nav_items .= "</ul>\n";
            }

            $nav_items .= "</li>\n";
        }
    }

    if (isset($nav_items)) {
        return $nav_items;
    }
}

/**
 * Build sub navigation helper - Builds sub navigation one level at a time
 *
 * @param   array       $nav_array A multi dimensional array with menu/sub menus links
 * @param   boolean     $nav_horizontal True if the menu is for horizontal view as well
 */
function build_sub_nav_array($nav_array, $nav_horizontal, $main_nav_active)
{
    $sub_nav_items = "";

    foreach ($nav_array as $node) {
        // Get all vital link info
        $link_name      = '<span class="nav-main-link-name">' . (isset($node['name']) ? $node['name'] : '') . '</span>' . "\n";
        $link_icon      = isset($node['icon']) ? '<i class="nav-main-link-icon ' . $node['icon'] . '"></i>' . "\n" : '';
        $link_badge     = isset($node['badge']) ? '<span class="nav-main-link-badge badge rounded-pill bg-' . (is_array($node['badge']) ? $node['badge'][1] : 'primary') . '">' . (is_array($node['badge']) ? $node['badge'][0] : $node['badge']) . '</span>' . "\n" : '';
        $link_url       = isset($node['url']) ? base_url($node['url']) : '#';
        $link_sub       = isset($node['sub']) && is_array($node['sub']) ? true : false;
        $link_type      = isset($node['type']) ? isset($node['type']) : '';
        $sub_active     = false;
        $link_active    = isset($node['url']) && $node['url'] == $main_nav_active ? true : false;

        // If link type is a header
        if ($link_type == 'heading') {
            $sub_nav_items .= "<li class=\"nav-main-heading\">" . (isset($node['name']) ? $node['name'] : '') . "</li>\n";
        } else {
            // If it is a submenu search for an active link in all sub links
            if ($link_sub) {
                $sub_active = build_nav_array_search($node['sub'], $main_nav_active) ? true : false;
            }

            // Set menu properties
            $li_prop        = ' class="nav-main-item' . ($sub_active && !$nav_horizontal ? ' open' : '') . '"';
            $link_prop      = ' class="nav-main-link' . ($link_active || ($sub_active && $nav_horizontal)  ? ' active' : '') . ($link_sub ? ' nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="' . ($sub_active ? 'true' : 'false') . '"' : '"');

            // Add the link
            $sub_nav_items .= "<li$li_prop>\n";
            $sub_nav_items .= "<a$link_prop href=\"$link_url\">\n$link_icon$link_name$link_badge</a>\n";

            // If it is a submenu, call the function again
            if ($link_sub) {
                $sub_nav_items .= "<ul class=\"nav-main-submenu\">\n";
                build_sub_nav_array($node['sub'], $nav_horizontal, $main_nav_active);
                $sub_nav_items .= "</ul>\n";
            }

            $sub_nav_items .= "</li>\n";
        }
    }

    if (isset($sub_nav_items)) {
        return $sub_nav_items;
    }
}

/**
 * Build navigation helper - Search navigation array for active menu links
 *
 * @param   array       $nav_array A multi dimensional array with menu/sub menus links
 *
 * @return  boolean     Returns true if an active link is found
 */
function build_nav_array_search($nav_array, $main_nav_active)
{
    foreach ($nav_array as $node) {
        if (isset($node['url']) && ($node['url'] == $main_nav_active)) {
            return true;
        } else if (isset($node['sub']) && is_array($node['sub'])) {
            if (build_nav_array_search($node['sub'], $main_nav_active)) {
                return true;
            }
        }
    }
}
