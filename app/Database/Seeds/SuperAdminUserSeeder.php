<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Entities\User;

class SuperAdminUserSeeder extends Seeder
{
    public function run()
    {
        $users = auth()->getProvider();

        // Create Super Admin User
        $user = new User([
            'username' => 'arulpatmose',
            'email'    => 'webmaster@itn.lk',
            'password' => 'uz`$8bqy>&f+',
            'first_name' => 'Arul Patmose',
            'last_name'  => 'Paramanathan',
        ]);
        $users->save($user);

        if ($user) {
            // Assign to superadmin group
            $user = $users->findById($users->getInsertID());
            $user->addGroup('superadmin');

            // Activate the User
            $user->activate();
        }
    }
}
