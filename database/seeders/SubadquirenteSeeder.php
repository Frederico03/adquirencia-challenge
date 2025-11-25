<?php

namespace Database\Seeders;

use App\Models\Subadquirente;
use Illuminate\Database\Seeder;

class SubadquirenteSeeder extends Seeder
{
    /**
     * Seed the subadquirentes table with the default handlers.
     */
    public function run(): void
    {
        $subadquirentes = [
            [
                'name' => 'subadq_a',
                'base_url' => config('services.subadquirentes.subadq_a.base_url'),
                'handler_class' => \App\Services\Adquirencia\SubadqA\SubadqAFactory::class,
                'is_active' => true,
            ],
            [
                'name' => 'subadq_b',
                'base_url' => config('services.subadquirentes.subadq_b.base_url'),
                'handler_class' => \App\Services\Adquirencia\SubadqB\SubadqBFactory::class,
                'is_active' => true,
            ],
        ];

        foreach ($subadquirentes as $subadquirente) {
            Subadquirente::updateOrCreate(
                ['name' => $subadquirente['name']],
                $subadquirente,
            );
        }
    }
}

