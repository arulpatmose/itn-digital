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
