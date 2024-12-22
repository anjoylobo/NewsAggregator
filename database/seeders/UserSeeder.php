<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This method seeds the `users` table by creating 10 active users
     * and 10 deleted users using the User factory.
     *
     * @return void
     */
    public function run(): void
    {
        // Creating 10 active users using the User factory
        User::factory()->count(10)->create();

        // Creating 10 deleted users using the User factory with the `deleted` scope
        User::factory()->count(10)->deleted()->create();
    }
}
