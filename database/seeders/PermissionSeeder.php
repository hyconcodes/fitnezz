<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Role management permissions
        $rolePermissions = [
            'view.roles',
            'create.roles',
            'edit.roles',
            'delete.roles',
            'assign.permissions',
        ];

        // Student management permissions
        $studentPermissions = [
            'view.students',
            'create.students',
            'edit.students',
            'delete.students',
        ];

        // Trainer management permissions
        $trainerPermissions = [
            'view.trainers',
            'create.trainers',
            'edit.trainers',
            'delete.trainers',
        ];

        // Equipment management permissions
        $equipmentPermissions = [
            'view.equipment',
            'create.equipment',
            'edit.equipment',
            'delete.equipment',
            'maintain.equipment',
        ];

        // Combine all permissions
        $permissions = array_merge(
            $studentPermissions,
            $trainerPermissions,
            $rolePermissions,
            $equipmentPermissions
        );
        // Create permissions in the database
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}