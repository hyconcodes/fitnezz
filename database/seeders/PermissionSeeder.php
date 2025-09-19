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

        // User management permissions
        // $userPermissions = [
        //     'view.users',
        //     'create.users',
        //     'edit.users',
        //     'delete.users',
        // ];

        // Medical record permissions
        // $medicalRecordPermissions = [
        //     'view.medical.records',
        //     'create.medical.records',
        //     'edit.medical.records',
        //     'delete.medical.records',
        // ];

        // Combine all permissions
        // $permissions = array_merge(
        //     $rolePermissions,
        //     $patientPermissions,
        //     $userPermissions,
        //     $medicalRecordPermissions
        // );

        // Combine all permissions
        $permissions = array_merge(
            $studentPermissions,
            $trainerPermissions,
            $rolePermissions
        );
        // Create permissions in the database
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}