<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'nama' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@ironsmart.local',
            'password' => Hash::make('admin123'),
            'role' => 'Admin',
        ]);
    }
}
