<?php

$config = config('Template');

$data['config']['title'] = ($page_title ?? $config->site_title) . " | ITN Digital";
$data['config']['og_url_site'] = base_url();
$data['config']['author'] = $config->author;
$data['config']['robots'] = $config->robots;
$data['config']['description'] = $config->description;
$data['config']['site_title'] = $config->site_title;
$data['config']['theme'] = $config->theme;
$data['config']['page_loader'] = $config->page_loader;

$data['config']['inc_header'] = '';
$data['config']['inc_hero'] = '';
$data['config']['inc_side_overlay'] = '';
$data['config']['inc_sidebar'] = '';

$data['config']['html_classes'] = html_classes($config, false);
$data['config']['page_classes'] = page_classes($config, false);

echo view('blank/master', $data);

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

    // Dark mode
    if ($config->l_dark_mode) {
        $page_classes .= ' dark-mode';
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
