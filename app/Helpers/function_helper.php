<?php

if (!function_exists('get_settings')) {
    /**
     * Retrieve settings from the database.
     *
     * @param string $type   The name of the settings variable to fetch (default: 'system_settings').
     * @param bool   $isJson Indicates whether the retrieved value should be decoded as JSON.
     * @return mixed         The decoded array/object if JSON, or the raw value if not.
     */
    function get_settings($group, $asArray = false)
    {
        $settingsService = service('settings');
        $fields = [
            'general' => [
                'siteName',
                'siteDescription',
                'metaKeywords',
                'metaDescription',
                'maintenanceMode',
                'maintenanceMessage'
            ],
            'email' => [
                'fromName',
                'fromEmail',
                'protocol',
                'SMTPHost',
                'SMTPPort',
                'SMTPUser',
                'SMTPPass',
                'SMTPCrypto'
            ],
            'system' => [
                'youtubeDataGoogleApi',
                'captchaSiteKey',
                'captchaSecret'
            ],
        ];

        $settings = [];
        $namespace = $group === 'email' ? 'Email' : 'App';
        if (isset($fields[$group])) {
            foreach ($fields[$group] as $key) {
                $settings[$key] = $settingsService->get("{$namespace}.{$key}");
            }
        }

        return $asArray ? $settings : (object) $settings;
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
