<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DevelopmentUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            ['name' => 'CityFix Administrator', 'email' => 'admin@cityfix.local', 'password' => 'admin123', 'role' => 'admin'],
            ['name' => 'Admin Sarpras', 'email' => 'verifier@cityfix.local', 'password' => 'password', 'role' => 'verifier'],
            ['name' => 'Petugas Maintenance', 'email' => 'technician@cityfix.local', 'password' => 'password', 'role' => 'technician'],
            ['name' => 'Pelapor Guru', 'email' => 'reporter@cityfix.local', 'password' => 'password', 'role' => 'reporter'],
        ];

        foreach ($users as $user) {
            User::firstOrCreate(
                ['email' => $user['email']],
                ['name' => $user['name'], 'password' => bcrypt($user['password']), 'role' => $user['role']],
            );
        }
    }
}
