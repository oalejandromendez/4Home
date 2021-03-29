<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'root',
            'lastname' => '',
            'email' => 'root@mail.com',
            'password' => bcrypt('123456'),
            'status' => 1
        ]);

        $user->assignRole('SUPERADMIN');
    }
}
