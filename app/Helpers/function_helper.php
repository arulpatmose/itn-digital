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

if (!function_exists('send_app_email')) {
    /**
     * Send an email using settings from DB.
     *
     * @param string|array $to Recipient email address(es)
     * @param string       $subject Email subject
     * @param string       $view View file path or raw HTML
     * @param array        $data Data to pass to the view
     * @param bool         $isView If true, $view is treated as a CI4 view file
     *
     * @return bool True if sent successfully, false otherwise
     */
    function send_app_email($to, string $subject, string $view, array $data = [], bool $isView = true): bool
    {
        helper(['email', 'settings']);

        // Get email settings from DB
        $emailSettings = get_settings('email', true);

        // Create emailer instance with DB config
        $email = emailer([
            'protocol'    => $emailSettings['protocol'] ?? 'smtp',
            'SMTPHost'    => $emailSettings['SMTPHost'] ?? '',
            'SMTPPort'    => $emailSettings['SMTPPort'] ?? 587,
            'SMTPUser'    => $emailSettings['SMTPUser'] ?? '',
            'SMTPPass'    => $emailSettings['SMTPPass'] ?? '',
            'SMTPCrypto'  => $emailSettings['SMTPCrypto'] ?? 'tls',
            'mailType'    => 'html',
            'charset'     => 'utf-8',
            'newline'     => "\r\n",
            'wordWrap'    => true,
        ])->setFrom(
            $emailSettings['fromEmail'],
            $emailSettings['fromName'] ?? ''
        );

        // Recipient(s)
        $email->setTo($to);

        // Subject
        $email->setSubject($subject);

        // Message
        if ($isView) {
            $email->setMessage(view($view, $data, ['debug' => true]));
        } else {
            $email->setMessage($view); // Treat as raw HTML string
        }

        // Send and handle errors
        if ($email->send(false) === false) {
            log_message('error', $email->printDebugger(['headers']));
            return false;
        }

        $email->clear();
        return true;
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

if (!function_exists('site_name')) {
    /**
     * Returns the site name from the settings service.
     *
     * @return string The configured site name or a default fallback.
     */
    function site_name(): string
    {
        return service('settings')->get('App.siteName') ?? '';
    }
}
