<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // User management
            'view users',
            'create users',
            'edit users',
            'delete users',

            // Membership management
            'view memberships',
            'create memberships',
            'edit memberships',
            'delete memberships',

            // Class management
            'view classes',
            'create classes',
            'edit classes',
            'delete classes',
            'book classes',

            // Payment management
            'view payments',
            'create payments',
            'edit payments',
            'delete payments',

            // Equipment management
            'view equipment',
            'create equipment',
            'edit equipment',
            'delete equipment',

            // Reports
            'view reports',
            'export reports',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles
        $superAdmin = Role::create(['name' => 'super-admin']);
        $admin = Role::create(['name' => 'admin']);
        $trainer = Role::create(['name' => 'trainer']);
        $student = Role::create(['name' => 'student']);

        // Assign permissions to roles
        $superAdmin->givePermissionTo(Permission::all());

        $admin->givePermissionTo([
            'view users', 'create users', 'edit users', 'delete users',
            'view memberships', 'create memberships', 'edit memberships', 'delete memberships',
            'view classes', 'create classes', 'edit classes', 'delete classes',
            'view payments', 'create payments', 'edit payments', 'delete payments',
            'view equipment', 'create equipment', 'edit equipment', 'delete equipment',
            'view reports', 'export reports',
        ]);

        $trainer->givePermissionTo([
            'view classes', 'create classes', 'edit classes', 'delete classes',
            'view equipment', 'create equipment', 'edit equipment', 'delete equipment',
            'view users', // To view assigned students
        ]);

        $student->givePermissionTo([
            'view classes', 'book classes',
            'view payments',
            'view equipment',
        ]);
    }
}
