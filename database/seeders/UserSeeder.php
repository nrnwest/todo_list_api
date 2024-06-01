<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /**
         * create demo user
         */
        $user = User::firstOrCreate([
            'email' => 'demo@demo.com',
        ], [
            'name' => 'Demo',
            'password' => Hash::make('demo'),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);

        User::factory(10)->create();
    }
}
