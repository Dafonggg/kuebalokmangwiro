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
        User::create([
            'name' => 'Sharul Nazwan',
            'email' => 'sharul@kuebalokstaff.com',
            'password' => Hash::make('12345678'),
            'role' => 'admin',
        ]);
        User::create([
            'name' => 'Vael',
            'email' => 'vael@kuebalokstaff.com',
            'password' => Hash::make('12345678'),
            'role' => 'admin',
        ]);
        User::create([
            'name' => 'Kimid',
            'email' => 'kimid@kuebalokstaff.com',
            'password' => Hash::make('12345678'),
            'role' => 'admin',
        ]);
        User::create([
            'name' => 'Arief A.K.A aip',
            'email' => 'aip@kuebalokstaff.com',
            'password' => Hash::make('12345678'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Kitchen Staff',
            'email' => 'dapur@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'kitchen',
        ]);
    }
}
