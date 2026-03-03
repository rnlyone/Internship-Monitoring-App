<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Admin user
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        // Sample intern users
        User::factory()->create([
            'name' => 'Intern One',
            'email' => 'intern1@example.com',
            'role' => 'intern',
        ]);

        User::factory()->create([
            'name' => 'Intern Two',
            'email' => 'intern2@example.com',
            'role' => 'intern',
        ]);

        // Default settings
        Setting::setValue('max_working_hours_per_week', 40);
        Setting::setValue('schedule_submission_open', '1');
    }
}
