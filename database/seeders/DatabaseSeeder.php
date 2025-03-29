<?php

namespace Database\Seeders;

use App\Enums\UserType;
use App\Models\User;
use App\Models\Website\Admin\Navigation;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'name' => 'website',
            'email' => 'website@admin.com',
            'type' => UserType::WebsiteAdmin->value,
            'password' => Hash::make(123456789),
        ]);

        Navigation::create([
            'menu' => 'Home',
        ]);
    }
}
