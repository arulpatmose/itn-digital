<?php

$config = config('Template');

$data['config']['title'] = $page_title ?? "";
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

$data['config']['page_classes'] = page_classes(false, $config);

echo view('layout/head_start', $data);
echo view('layout/head_end', $data);
echo view('layout/page_start', $data);
echo view('layout/page_content', $data);
echo view('layout/page_end', $data);
echo view('layout/footer_start', $data);
echo view('layout/footer_scripts', $data);

/**
 * Builds #page-container classes
 *
 * @param   boolean $print True to print the classes and False to return them
 *
 * @return  string  Returns the classes if $print is set to false
 */
function page_classes($print = true, $config)
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
