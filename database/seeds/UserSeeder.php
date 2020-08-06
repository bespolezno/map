<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'username' => 'admin',
                'password' => Hash::make('adminpass'),
                'is_admin' => true
            ],
            [
                'username' => 'user1',
                'password' => Hash::make('user1pass'),
                'is_admin' => false
            ],
            [
                'username' => 'user2',
                'password' => Hash::make('user2pass'),
                'is_admin' => false
            ]
        ];

        collect($users)->each([User::class, 'create']);
    }
}
