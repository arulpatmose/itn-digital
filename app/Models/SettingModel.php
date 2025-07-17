<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table            = 'system_settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['variable', 'value'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Update grouped settings (web, system, social)
     */
    public function updateGroupedSettings(array $post)
    {
        helper('text');

        // Initialize setting buckets
        $systemSettings  = [];

        // Keys grouped by form
        $systemKeys = [
            'captcha-sitekey',
            'captcha-secret',
            'youtube-data-google-api',
        ];

        // Process submitted POST data
        foreach ($post as $key => $value) {
            if (in_array($key, $systemKeys)) {
                $systemSettings[$key] = esc($value);
            }
        }

        // Conditionally save each settings group
        if (!empty($systemSettings)) {
            $this->saveSetting('system_settings', json_encode($systemSettings));
        }

        return true;
    }

    /**
     * Save setting by variable
     */
    protected function saveSetting(string $variable, string $value)
    {
        $existing = $this->where('variable', $variable)->first();
        if ($existing) {
            $this->update($existing['id'], ['value' => $value]);
        } else {
            $this->insert(['variable' => $variable, 'value' => $value]);
        }
    }

    /**
     * Get settings by variable
     */
    public function getSetting(string $variable, bool $decode = true)
    {
        $setting = $this->where('variable', $variable)->first();
        if (!$setting) return null;

        return $decode ? json_decode($setting['value'], true) : $setting['value'];
    }
}
