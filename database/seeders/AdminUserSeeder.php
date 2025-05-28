<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::create([
            'name_en' => 'Admin User',
            'name_ar' => 'سوبر ادمن',
            'email' => 'admin@admin.com',
            'password' => Hash::make(12345678), // Change this password!
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        // Assign admin role to the user
        $adminUser->assignRole('super-admin');

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@admin.com');
        $this->command->info('Password: password (12345678)');
    }
}
