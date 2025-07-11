<?php

if (!function_exists('formatName')) {
    function formatName($name)
    {
        // Trim the name to remove any leading or trailing whitespace
        $name = trim($name);

        // Convert the name to lowercase
        $name = strtolower($name);

        // Split into parts by space to separate initials and last name
        $parts = explode(' ', $name);

        // If there's only one part, assume it's a single name without initials
        if (count($parts) == 1) {
            return ucfirst($name);
        }

        $initialsPart = '';
        $lastNameParts = [];

        foreach ($parts as $part) {
            if (strpos($part, '.') !== false) {
                // It's an initial part, ensure it's formatted properly
                $initials = explode('.', $part);
                foreach ($initials as $initial) {
                    if (!empty($initial)) {
                        $initialsPart .= strtoupper($initial) . '. ';
                    }
                }
            } else {
                // It's part of the last name
                $lastNameParts[] = ucfirst($part);
            }
        }

        // Trim any extra space from the initials part
        $initialsPart = trim($initialsPart);
        $lastName = implode(' ', $lastNameParts);

        // Combine the formatted initials and last name
        $formattedName = $initialsPart . ' ' . $lastName;

        return trim($formattedName);
    }
}
