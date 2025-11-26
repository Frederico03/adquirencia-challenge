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
        $user1 = User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
            ],
        );

        $user2 = User::updateOrCreate(
            ['email' => 'test2@example.com'],
            [
                'name' => 'Test User 2',
                'password' => Hash::make('password'),
            ],
        );

        $subadquirentes = Subadquirente::pluck('id');

        if ($subadquirentes->isNotEmpty()) {
            $user1->subadquirentes()->syncWithoutDetaching($subadquirentes->toArray()[0]);
            $user2->subadquirentes()->syncWithoutDetaching($subadquirentes->toArray()[1]);
        }
    }
}

