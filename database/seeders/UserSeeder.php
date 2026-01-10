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
        // Get or create roles (should already exist from RolePermissionSeeder)
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $receptionistRole = Role::firstOrCreate(['name' => 'receptionist']);
        $employeeRole = Role::firstOrCreate(['name' => 'employee']);

        // Create Admin User
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'position' => 'Admin',
            'department' => 'Management',
            'password' => Hash::make('12345678'),
        ]);
        $admin->assignRole($adminRole);

        // Create Receptionist User
        $receptionist = User::factory()->create([
            'name' => 'Receptionist User',
            'email' => 'receptionist@gmail.com',
            'position' => 'Receptionist',
            'department' => 'Front Office',
            'password' => Hash::make('12345678'),
        ]);
        $receptionist->assignRole($receptionistRole);

        // Create Employee User
        $employee = User::factory()->create([
            'name' => 'Employee User',
            'email' => 'employee@gmail.com',
            'position' => 'Staff',
            'department' => 'IT',
            'password' => Hash::make('12345678'),
        ]);
        $employee->assignRole($employeeRole);

        $this->command->info('Created 3 test users:');
        $this->command->info('- Admin: admin@gmail.com / 12345678');
        $this->command->info('- Receptionist: receptionist@gmail.com / 12345678');
        $this->command->info('- Employee: employee@gmail.com / 12345678');
    }
}