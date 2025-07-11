<?php

if (!function_exists('formatCompanyName')) {
    function formatCompanyName($name)
    {
        // Trim the name to remove any leading or trailing whitespace
        $name = trim($name);

        // Convert the name to lowercase for initial processing
        $name = strtolower($name);

        // Convert the entire name to proper case if it was all uppercase
        $name = ucwords($name);

        // List of common company suffixes in Sri Lanka
        $suffixes = ['Pvt Ltd', 'PLC', 'LLC', 'Ltd', 'Inc', 'Corporation'];

        // Split the name into parts to handle initials and suffixes separately
        $parts = explode(' ', $name);

        $formattedName = '';
        $initialsPart = '';
        $lastNameParts = [];
        $suffixPart = '';
        $isSuffix = false;

        foreach ($parts as $part) {
            if (strpos($part, '.') !== false && !empty(trim($part, '.'))) {
                // It's an initial part
                $initials = explode('.', $part);
                foreach ($initials as $initial) {
                    if (!empty($initial)) {
                        $initialsPart .= strtoupper($initial) . '. ';
                    }
                }
            } elseif (in_array(strtoupper($part), $suffixes)) {
                // Handle company suffixes
                $suffixPart = strtoupper($part);
                $isSuffix = true;
            } else {
                // It's part of the last name or other name part
                $lastNameParts[] = ucfirst($part);
            }
        }

        // Join formatted initials and last name parts
        $initialsPart = trim($initialsPart);
        $lastName = implode(' ', $lastNameParts);

        // Combine initials, last name, and suffix
        if ($initialsPart) {
            $formattedName = $initialsPart . ' ' . $lastName;
        } else {
            $formattedName = $lastName;
        }

        // Add suffix if present, without periods
        if ($suffixPart) {
            $formattedName .= ' ' . $suffixPart;
        }

        // Remove any unwanted periods from the formatted name
        $formattedName = str_replace(' .', ' ', $formattedName);
        $formattedName = str_replace('.', '', $formattedName);

        return trim($formattedName);
    }
}
