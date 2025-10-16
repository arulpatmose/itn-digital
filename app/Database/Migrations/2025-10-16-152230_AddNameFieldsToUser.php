<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Forge;
use CodeIgniter\Database\Migration;

class AddNameFieldsToUser extends Migration
{
    /**
     * @var string[] The tables to modify based on the Auth configuration
     */
    private array $tables;

    public function __construct(?Forge $forge = null)
    {
        parent::__construct($forge);

        /** @var \Config\Auth $authConfig */
        $authConfig   = config('Auth');
        $this->tables = $authConfig->tables;
    }

    public function up()
    {
        // Define the columns to be added
        $fields = [
            'first_name' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true],
            'last_name'  => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true],
        ];

        // Add the new columns to the users table
        $this->forge->addColumn($this->tables['users'], $fields);
    }

    public function down()
    {
        // Drop the columns if migrating down
        $fields = ['first_name', 'last_name'];
        $this->forge->dropColumn($this->tables['users'], $fields);
    }
}
