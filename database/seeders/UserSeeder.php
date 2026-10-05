<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Alice Employee',
            'email' => 'employee@deskflow.test',
            'password' => Hash::make('password'),
            'role' => 'employee',
        ]);

        User::create([
            'name' => 'Bob Agent',
            'email' => 'agent@deskflow.test',
            'password' => Hash::make('password'),
            'role' => 'agent',
        ]);

        User::create([
            'name' => 'Carol Manager',
            'email' => 'manager@deskflow.test',
            'password' => Hash::make('password'),
            'role' => 'manager',
        ]);

        User::factory()->count(20)->create();
    }
}
