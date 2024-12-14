<?php

namespace Database\Seeders;

use App\Models\User;
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
        if(!User::where('email', 'lucas@test.com')->first()) {
            $superAdmin = User::create([
                'name' => 'Lucas',
                'email' => 'lucas@test.com',
                'password' => Hash::make('123456', ['rounds' => '12'])
            ]);
        }
    }
}
