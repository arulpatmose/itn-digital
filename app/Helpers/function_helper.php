<?php

if (!function_exists('get_settings')) {
    /**
     * Fetch settings from the database.
     *
     * @param string $type     The variable name of the settings (default: 'system_settings').
     * @param bool   $isJson   Whether to decode JSON or not.
     * @return mixed           Returns decoded array/object if JSON, raw value otherwise.
     */
    function get_settings(string $type = 'system_settings', bool $isJson = false)
    {
        $db = \Config\Database::connect();

        $builder = $db->table('system_settings');
        $res = $builder->select('*')
            ->where('variable', $type)
            ->get()
            ->getResultArray();

        if (!empty($res)) {
            $value = $res[0]['value'];

            if ($isJson) {
                return json_decode($value, true);
            } else {
                return esc($value);
            }
        }

        return null;
    }
}

if (!function_exists('areAllItemsPublished')) {
    /**
     * Check if all items in the dataset are published.
     *
     * This function loops through the provided data array and verifies whether
     * every item's specified value column (e.g., 'published') is set to 1.
     * If any item is not published (value not equal to 1), it returns false.
     *
     * @param array $data The dataset to evaluate (each item is an associative array).
     * @param string $valueColumn The column name to check for the published status.
     *
     * @return bool Returns true if all items are published (value = 1), otherwise false.
     */
    function areAllItemsPublished(array $data, string $valueColumn): bool
    {
        if (empty($data)) {
            return false; // no items means not published
        }

        foreach ($data as $row) {
            // Cast to int for safety
            if ((int) $row[$valueColumn] !== 1) {
                return false; // found an unpublished item
            }
        }

        return true; // all published
    }
}

if (!function_exists('validateDate')) {
    /**
     * Validate a date string against a specific format.
     *
     * @param string $date The date string to validate.
     * @param string $format The format to validate against (default is 'Y-m-d').
     * @return bool True if the date is valid, false otherwise.
     */
    function validateDate($date, $format = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }
}
