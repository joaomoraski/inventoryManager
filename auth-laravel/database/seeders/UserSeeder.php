<?php

namespace Database\Seeders;

use App\Models\User;
use App\Traits\UUID;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            "name" => "System Admin",
            "email" => env("ADMIN_EMAIL"),
            "password" => Hash::make(env("ADMIN_PASSWORD")),
        ]);
        $user->assignRole("admin");
    }
}
