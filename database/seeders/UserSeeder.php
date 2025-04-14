<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::create(['name' => 'admin']);

        User::factory(20)->create(); // dynamic 
    
        $adminRole = User::factory()->create([ // statistics
            'name' => 'Test User',
            'email' => 'qilz@gmail.com',
            'position' => 'CEO',
            'department' => 'IT',
            'password' => Hash::make('12345678'),
        ]);

        $adminRole->assignRole($role);
    }
}