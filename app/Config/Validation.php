<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Validation\StrictRules\CreditCardRules;
use CodeIgniter\Validation\StrictRules\FileRules;
use CodeIgniter\Validation\StrictRules\FormatRules;
use CodeIgniter\Validation\StrictRules\Rules;

class Validation extends BaseConfig
{
    // --------------------------------------------------------------------
    // Setup
    // --------------------------------------------------------------------

    /**
     * Stores the classes that contain the
     * rules that are available.
     *
     * @var string[]
     */
    public array $ruleSets = [
        Rules::class,
        FormatRules::class,
        FileRules::class,
        CreditCardRules::class,
    ];

    /**
     * Specifies the views that are used to display the
     * errors.
     *
     * @var array<string, string>
     */
    public array $templates = [
        'list'   => 'CodeIgniter\Validation\Views\list',
        'single' => 'CodeIgniter\Validation\Views\single',
    ];

    // --------------------------------------------------------------------
    // Rules
    // --------------------------------------------------------------------

    public $registration = [
        'username' => [
            'label' => 'Auth.username',
            'rules' => [
                'required',
                'max_length[30]',
                'min_length[3]',
                'regex_match[/\A[a-zA-Z0-9\.]+\z/]',
                'is_unique[users.username]',
            ],
        ],
        'first_name' => [
            'label' => 'First Name',
            'rules' => [
                'max_length[60]',
                'min_length[3]',
            ],
        ],
        'last_name' => [
            'label' => 'Last Name',
            'rules' => [
                'max_length[60]',
                'min_length[3]',
            ],
        ],
        'email' => [
            'label' => 'Auth.email',
            'rules' => [
                'required',
                'max_length[254]',
                'valid_email',
                'is_unique[auth_identities.secret]',
            ],
        ],
        'password' => [
            'label' => 'Auth.password',
            'rules' => 'required|strong_password',
        ],
        'password_confirm' => [
            'label' => 'Auth.passwordConfirm',
            'rules' => 'required|matches[password]',
        ],
    ];

    public $registration_errors = [
        'username'            =>     [
            'required'         => 'Enter your Unique Username.',
            'max_length'     => 'Username must not exceed 30 characters.',
            'min_length'     => 'Username must be atleast 3 characters long.',
            'regex_match'   => 'Username should contain only numbers and letters.',
            'is_unique'     => 'Already exists, enter something else'
        ],
        'email'              => [
            'required'         => 'Enter your Email.',
            'max_length'     => 'Too long, try with another email.',
            'is_unique'        => 'Email already exists. Try another',
            'valid_email'     => 'Please enter a valid email address.',
        ],
        'first_name'        => [
            'required'         => 'Please enter First Name.',
            'alpha'         => 'Provide a proper name',
            'min_length'     => 'Too short!! Name must be atleast 3 characters long.',
        ],
        'last_name'         => [
            'required'         => 'Please enter Last Name.',
            'alpha'         => 'Provide a proper name',
            'min_length'     => 'Too short!! Name must be atleast 3 characters long.',
        ],
        'password'            =>     [
            'required'         => 'Enter your Password.',
            'min_length'     => 'Password must be atleast 8 digits.',
            'strong_password' => 'Password must be strong'
        ],
        'password_confirm'                =>    [
            'required'     => 'Re-enter your password.',
            'matches'     => 'Confirm password and password must be same.'
        ]
    ];
}
