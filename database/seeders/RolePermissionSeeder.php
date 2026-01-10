<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User management
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // Attendance management
            'view attendance',
            'create attendance',
            'edit attendance',
            'delete attendance',
            'export attendance',
            
            // QR Code management
            'generate qr code',
            'deactivate qr code',
            'view qr code',
            
            // Permission/Permit management
            'view permissions',
            'approve permissions',
            'reject permissions',
            
            // Company management
            'view company',
            'edit company',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions

        // 1. Admin Role - Full access
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // 2. Receptionist Role - Can generate QR codes and view attendance
        $receptionistRole = Role::firstOrCreate(['name' => 'receptionist']);
        $receptionistRole->givePermissionTo([
            'generate qr code',
            'deactivate qr code',
            'view qr code',
            'view attendance',
            'view users',
        ]);

        // 3. Employee Role - Basic access
        $employeeRole = Role::firstOrCreate(['name' => 'employee']);
        $employeeRole->givePermissionTo([
            'view attendance',
            'create attendance',
            'view permissions',
        ]);

        $this->command->info('Roles and permissions seeded successfully!');
    }
}
