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
