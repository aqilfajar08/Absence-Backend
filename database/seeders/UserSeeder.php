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
        // Ensure Roles exist
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $staffRole = Role::firstOrCreate(['name' => 'staff']);
        $resepsionisRole = Role::firstOrCreate(['name' => 'resepsionis']); // Restore Resepsionis Role
        
        // 1. Create Admin (Essential)
        $admin = User::updateOrCreate([
            'email' => 'admin@gmail.com'
        ], [
            'name' => 'Admin',
            'position' => 'Admin',
            'department' => 'IT',
            'password' => Hash::make('admin@kasau'),
            'gaji_pokok' => 0,
            'tunjangan' => 0,
        ]);
        $admin->syncRoles($adminRole);

        // 2. Create Employees from List
        $employees = [
            ['name' => 'Rano M', 'email' => 'rano@gmail.com', 'dept' => 'Ops-Mtc', 'pass' => 'rano@kasau'],
            ['name' => 'Rohana', 'email' => 'rohana@gmail.com', 'dept' => 'Ops-Acc', 'pass' => 'rohana@kasau'],
            ['name' => 'Idris', 'email' => 'idris@gmail.com', 'dept' => 'OPS Support', 'pass' => 'idris@kasau'],
            ['name' => 'Kory T', 'email' => 'kory@gmail.com', 'dept' => 'Sec', 'pass' => 'kory@kasau', 'role' => 'resepsionis'], // Changed to Resepsionis
            ['name' => 'Pahira', 'email' => 'pahira@gmail.com', 'dept' => 'ACC', 'pass' => 'pahira@kasau'],
            ['name' => 'Ratna', 'email' => 'ratna@gmail.com', 'dept' => 'Ops-Mtc', 'pass' => 'ratna@kasau'],
            ['name' => 'Rahmah', 'email' => 'rahmah@gmail.com', 'dept' => 'Pro-log', 'pass' => 'rahmah@kasau'],
            ['name' => 'Lutfi L', 'email' => 'lutfi@gmail.com', 'dept' => 'HR-GA-HS', 'pass' => 'lutfi@kasau'],
            ['name' => 'Karina', 'email' => 'karina@gmail.com', 'dept' => 'Legal', 'pass' => 'karina@kasau'],
            ['name' => 'Tetdi G', 'email' => 'tetdi@gmail.com', 'dept' => 'GA', 'pass' => 'tetdi@kasau'],
            ['name' => 'Rusdiana', 'email' => 'rusdiana@gmail.com', 'dept' => 'FINANCE', 'pass' => 'rusdiana@kasau'],
        ];

        foreach ($employees as $emp) {
            $user = User::updateOrCreate([
                'email' => $emp['email']
            ], [
                'name' => $emp['name'],
                'position' => isset($emp['role']) && $emp['role'] == 'resepsionis' ? 'Resepsionis' : 'Karyawan',
                'department' => $emp['dept'],
                'password' => Hash::make($emp['pass']),
                'gaji_pokok' => 0,
                'tunjangan' => 0,
            ]);
            
            $roleName = $emp['role'] ?? 'staff';
            $user->syncRoles($roleName);
        }

        $this->command->info('Seed completed: Admin + 11 Karyawan created.');
    }
}