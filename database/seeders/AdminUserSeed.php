<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::where('name', 'ADMINISTRATOR')->first();

        if(empty($role)) {
            $role = new Role();
            $role->name = 'ADMINISTRATOR';
            $role->display_name = 'ADMINISTRATOR';
            $role->save();
        }

        $user = User::where('email', 'admin@mail.com')->first();

        if (empty($user)) {
            $user = new User();
            $user->name = 'ADMINISTRATOR';
            $user->email = 'admin@mail.com';
            $user->password = Hash::make('123456');
            $user->save();
        }

    }
}
