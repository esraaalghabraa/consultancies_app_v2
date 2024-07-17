<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::create([
            'first_name'=>'Esraa',
            'last_name'=>'Alghabra',
            'email'=>'esraaalghabra3040@gmail.com',
            'password'=>Hash::make('12345678'),
            'email_verified_at'=>now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
