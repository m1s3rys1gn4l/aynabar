<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate([
            'email' => env('SEED_SUPERADMIN_EMAIL', 'admin@example.com'),
        ], [
            'name' => env('SEED_SUPERADMIN_NAME', 'Super Admin'),
            'password' => env('SEED_SUPERADMIN_PASSWORD', 'ChangeMe123!'),
            'role' => 'SUPERADMIN',
        ]);
    }
}
