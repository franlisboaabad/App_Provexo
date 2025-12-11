<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Seed roles first
        $this->call(RoleSeeder::class);

        // Create admin user
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'frank@admin.com',
            'email_verified_at' => now(),
            'password' => bcrypt('secret'),
        ])->assignRole('Admin');

    }
}
