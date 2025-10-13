<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SettingModel;
use CodeIgniter\HTTP\ResponseInterface;

class Settings extends BaseController
{
    protected $categoryNamespaces;

    public function __construct()
    {
        // Define category to namespace mapping
        $this->categoryNamespaces = [
            'general' => 'App',
            'system'  => 'App',
            'email'   => 'Email',
            'auth'    => 'Auth', // new
        ];
    }

    public function index($category = null)
    {
        // Authorization using Shield
        if (!auth()->user()->can('admin.settings')) {
            return redirect()->back()->with('error', 'You are not allowed to view this page!');
        }

        // If no category is specified, show the tiled index view
        if ($category === null) {
            $data = [
                'page_title' => 'Website Settings',
                'page_description' => 'Manage Website Settings',
                'categories' => [
                    'general' => [
                        'title'       => 'General Settings',
                        'description' => 'Manage site identity and maintenance settings',
                        'icon'        => 'fa-globe',
                    ],
                    'email' => [
                        'title'       => 'Email Settings',
                        'description' => 'Configure email server and sender details',
                        'icon'        => 'fa-envelope',
                    ],
                    'system' => [
                        'title'       => 'System Settings',
                        'description' => 'Control core system behavior, logging, and performance options',
                        'icon'        => 'fa-cogs',
                    ],
                    'auth' => [
                        'title'       => 'Authentication Settings',
                        'description' => 'Manage login, registration, and security configurations',
                        'icon'        => 'fa-user-shield',
                    ],
                ],
            ];
            return view('backend/settings/index', $data);
        }

        $settingsService = service('settings');

        // Define fields for each category
        $fields = [
            'general' => [
                'siteName' => ['type' => 'text', 'label' => 'Site Name'],
                'siteDescription' => ['type' => 'textarea', 'label' => 'Site Description'],
                'metaKeywords' => ['type' => 'text', 'label' => 'Meta Keywords'],
                'metaDescription' => ['type' => 'text', 'label' => 'Meta Description'],
                'maintenanceMode' => [
                    'type' => 'select',
                    'label' => 'Maintenance Mode',
                    'options' => ['0' => 'Off', '1' => 'On']
                ],
                'maintenanceMessage' => ['type' => 'textarea', 'label' => 'Maintenance Message'],
            ],

            'email' => [
                'fromName' => ['type' => 'text', 'label' => 'Email From Name'],
                'fromEmail' => ['type' => 'text', 'label' => 'Email From Address'],
                'protocol' => [
                    'type' => 'select',
                    'label' => 'Email Protocol',
                    'options' => ['mail' => 'Mail', 'sendmail' => 'Sendmail', 'smtp' => 'SMTP']
                ],
                'SMTPHost' => ['type' => 'text', 'label' => 'SMTP Host'],
                'SMTPPort' => ['type' => 'number', 'label' => 'SMTP Port'],
                'SMTPUser' => ['type' => 'text', 'label' => 'SMTP Username'],
                'SMTPPass' => ['type' => 'password', 'label' => 'SMTP Password'],
                'SMTPCrypto' => [
                    'type' => 'select',
                    'label' => 'Email Encryption',
                    'options' => ['tls' => 'TLS', 'ssl' => 'SSL', 'none' => 'None']
                ],
            ],

            'system' => [
                'youtubeDataGoogleApi' => [
                    'type' => 'text',
                    'label' => 'YouTube Data Google API Key',
                    'placeholder' => 'Enter YouTube Data Google API Key'
                ],
                'captchaSiteKey' => [
                    'type' => 'text',
                    'label' => 'Google reCAPTCHA Site Key',
                    'placeholder' => 'Enter Captcha Site Key'
                ],
                'captchaSecret' => [
                    'type' => 'text',
                    'label' => 'Google reCAPTCHA Secret',
                    'placeholder' => 'Enter Captcha Secret'
                ],
            ],

            'auth' => [
                'allowRegistration' => [
                    'type' => 'select',
                    'label' => 'Allow User Registration',
                    'options' => ['1' => 'Enabled', '0' => 'Disabled'],
                    'default' => '1'
                ],
            ],
        ];

        // Retrieve current settings values
        $settings = [];

        // Map categories to namespaces
        $namespace = $this->categoryNamespaces[$category] ?? 'App'; // default to App if not mapped

        foreach ($fields[$category] as $key => $field) {
            $settings[$key] = $settingsService->get("{$namespace}.{$key}");
        }

        $data = [
            'page_title' => "Settings",
            'page_description' => "Manage Settings",
            'settings' => $settings,
            'settingGroup' => $category,
            'categories' => ['general', 'email', 'system'],
            'fields' => $fields[$category],
        ];

        return view('backend/settings/forms/' . $category, $data);
    }

    public function update()
    {
        // Authorization using Shield
        if (!auth()->user()->can('admin.settings')) {
            return redirect()->back()->with('error', 'You are not allowed to view this page!');
        }

        $settingsService = service('settings');
        $category        = $this->request->getPost('group');
        $settings        = $this->request->getPost('settings', FILTER_DEFAULT);

        // Map categories to namespaces
        $namespace = $this->categoryNamespaces[$category] ?? 'App'; // default to App if unmapped

        // Define field types per category for type casting
        $boolFieldsPerCategory = [
            'general' => ['maintenanceMode'],
            'auth'    => ['allowRegistration'],
        ];

        $intFieldsPerCategory = [
            'email' => ['SMTPPort'],
            'system' => ['maxCategoriesPerJobSeeker'],
        ];

        $boolFields = $boolFieldsPerCategory[$category] ?? [];
        $intFields  = $intFieldsPerCategory[$category] ?? [];

        foreach ($settings as $key => $value) {
            // Cast boolean fields
            if (in_array($key, $boolFields)) {
                $value = $value === '1' ? true : false;
            }

            // Cast integer fields
            if (in_array($key, $intFields)) {
                $value = (int) $value;
            }

            $settingsService->set("{$namespace}.{$key}", $value);
        }

        return redirect()->to('settings/' . $category)
            ->with('success', 'Settings updated successfully');
    }

    public function forget($category, $key)
    {
        // Authorization using Shield
        if (!auth()->user()->can('admin.settings')) {
            return redirect()->back()->with('error', 'You are not allowed to view this page!');
        }

        // Map categories to namespaces
        $namespace = $this->categoryNamespaces[$category] ?? 'App'; // default to App if unmapped

        service('settings')->forget("{$namespace}.{$key}");

        return redirect()->to('settings/' . $category)
            ->with('message', "Setting {$key} has been reset to default");
    }
}
