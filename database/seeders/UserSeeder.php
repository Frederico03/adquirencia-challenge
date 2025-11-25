<?php

namespace Database\Seeders;

use App\Models\Subadquirente;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the users table with a default test account and attach all
     * available subadquirentes to it.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
            ],
        );

        $subadquirentes = Subadquirente::pluck('id');

        if ($subadquirentes->isNotEmpty()) {
            $user->subadquirentes()->syncWithoutDetaching($subadquirentes->all());
        }
    }
}

