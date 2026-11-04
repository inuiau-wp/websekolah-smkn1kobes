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
        $this->call([
            JurusanSeeder::class,
            RaporPendidikanSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Admin SMKN 1 Kotabesi',
            'email' => 'admin@smkn1kotabesi.sch.id',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
    }
}
