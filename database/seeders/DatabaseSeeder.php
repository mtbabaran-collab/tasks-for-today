<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database: one demo account (see README) and sample tasks.
     */
    public function run(): void
    {
        // Created directly (not with the factory) so seeding also works on the server,
        // where the Faker package used by factories is not installed.
        User::updateOrCreate(
            ['email' => 'demo@tasksfortoday.test'],
            ['name' => 'Demo User', 'password' => Hash::make('password'), 'email_verified_at' => now()],
        );

        $this->call(TaskSeeder::class);
    }
}
