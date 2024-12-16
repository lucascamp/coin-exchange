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
                'name' => 'Lucas Campos',
                'email' => 'lucas@test.com',
                'password' => Hash::make('123456', ['rounds' => '12'])
            ]);
        }

        if(!User::where('email', 'leonardo@test.com')->first()) {
            $superAdmin = User::create([
                'name' => 'Leonardo Boquinha',
                'email' => 'leonardo@test.com',
                'password' => Hash::make('123456', ['rounds' => '12'])
            ]);
        }

        if(!User::where('email', 'jose@test.com')->first()) {
            $superAdmin = User::create([
                'name' => 'Jose da silva',
                'email' => 'jose@test.com',
                'password' => Hash::make('123456', ['rounds' => '12'])
            ]);
        }
    }
}
