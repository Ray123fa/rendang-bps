<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superadmin = [
            'name' => 'Super Admin',
            'email' => 'superadmin@mail.com',
            'password' => bcrypt('superadmin123'),
        ];
        $superadmin = User::create($superadmin);
        $superadmin->assignRole('super_admin');

        $kabkot = [
            'name' => 'BPS Banjarmasin',
            'email' => 'bpsbjm@mail.com',
            'password' => bcrypt('bpsbjm123'),
        ];
        $kabkot = User::create($kabkot);
        $kabkot->assignRole('kabkot');
    }
}
