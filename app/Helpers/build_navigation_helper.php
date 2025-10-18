<?php

use CodeIgniter\Shield\Authorization\AuthorizationTrait;

if (!function_exists('build_nav')) {
    /**
     * Builds navigation HTML output based on user permissions.
     */
    function build_nav(string $main_nav_active, ?array $nav_array = null, bool $nav_horizontal = false, bool $print = true)
    {
        if ($nav_array === null) {
            $nav_array = config('App')->main_nav ?? [];
        }

        $nav_html = build_nav_array($nav_array, $nav_horizontal, $main_nav_active);

        if ($print) {
            echo $nav_html;
        } else {
            return $nav_html;
        }
    }
}

if (!function_exists('build_nav_array')) {
    /**
     * Builds nav HTML from array.
     */
    function build_nav_array(array $nav_array, bool $nav_horizontal, string $main_nav_active): string
    {
        $nav_items = '';

        foreach ($nav_array as $node) {
            // Skip if user doesn't have permission
            if (isset($node['permission']) && !auth()->user()->can($node['permission'])) {
                continue;
            }

            $link_type = $node['type'] ?? 'nav-item'; // Default to nav-item if not specified

            // Handle nav-section type
            if ($link_type === 'nav-section') {
                // Check if this section has any visible children
                $hasVisibleChildren = has_visible_children($node, $nav_array);

                if ($hasVisibleChildren) {
                    $nav_items .= '<li class="nav-main-heading">' . esc($node['name']) . '</li>';
                }
                continue;
            }

            // Handle nav-item type (original logic)
            $link_url    = isset($node['url']) ? base_url($node['url']) : '#';
            $link_name   = '<span class="nav-main-link-name">' . esc($node['name'] ?? '') . '</span>';
            $link_icon   = isset($node['icon']) ? '<i class="nav-main-link-icon ' . esc($node['icon']) . '"></i>' : '';
            $link_badge  = isset($node['badge']) ? '<span class="nav-main-link-badge badge rounded-pill bg-' . (is_array($node['badge']) ? $node['badge'][1] : 'primary') . '">' . (is_array($node['badge']) ? $node['badge'][0] : $node['badge']) . '</span>' : '';
            $link_sub    = !empty($node['sub']) && is_array($node['sub']);
            $sub_active  = $link_sub ? build_nav_array_search($node['sub'], $main_nav_active) : false;
            $link_active = isset($node['url']) && $node['url'] === $main_nav_active;

            $li_class  = ' class="nav-main-item' . ($sub_active && !$nav_horizontal ? ' open' : '') . '"';
            $a_class   = ' class="nav-main-link' . ($link_active || ($sub_active && $nav_horizontal) ? ' active' : '') . ($link_sub ? ' nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="' . ($sub_active ? 'true' : 'false') . '"' : '"');

            $nav_items .= "<li$li_class><a$a_class href=\"$link_url\">$link_icon$link_name$link_badge</a>";

            if ($link_sub) {
                $nav_items .= '<ul class="nav-main-submenu">' . build_nav_array($node['sub'], $nav_horizontal, $main_nav_active) . '</ul>';
            }

            $nav_items .= '</li>';
        }

        return $nav_items;
    }
}

if (!function_exists('has_visible_children')) {
    /**
     * Check if a nav-section has any visible children items.
     * This function looks for nav-items that come after the section and belong to it.
     */
    function has_visible_children(array $section, array $nav_array): bool
    {
        $section_name = $section['name'] ?? '';
        $found_section = false;

        foreach ($nav_array as $node) {
            // Skip permission check for sections
            if (($node['type'] ?? 'nav-item') === 'nav-section') {
                if ($node['name'] === $section_name) {
                    $found_section = true;
                    continue;
                }

                // If we found another section, stop looking
                if ($found_section) {
                    break;
                }
            }

            // If we're in the section and found a nav-item with permission, return true
            if ($found_section && ($node['type'] ?? 'nav-item') === 'nav-item') {
                // Check if user has permission for this item
                if (!isset($node['permission']) || auth()->user()->can($node['permission'])) {
                    return true;
                }
            }
        }

        return false;
    }
}

if (!function_exists('build_nav_array_search')) {
    /**
     * Recursively search if active link exists in subnav.
     */
    function build_nav_array_search(array $nav_array, string $main_nav_active): bool
    {
        foreach ($nav_array as $node) {
            // Skip sections in submenu search
            if (($node['type'] ?? 'nav-item') === 'nav-section') {
                continue;
            }

            if (isset($node['url']) && $node['url'] === $main_nav_active) {
                return true;
            }

            if (isset($node['sub']) && is_array($node['sub'])) {
                if (build_nav_array_search($node['sub'], $main_nav_active)) {
                    return true;
                }
            }
        }

        return false;
    }
}
