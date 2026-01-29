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
        User::updateOrCreate([
            'username' => '17056535',
        ], [
            'name' => 'Admin Analitica',
            'email' => 'admin@saludaysen.cl',
            'password' => bcrypt('123456'),
        ]);

        $this->call(DatasetSeeder::class);
    }
}
