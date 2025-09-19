<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'super-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a super admin user with all permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating super admin user...');

        // Create or get the super-admin role
        $role = Role::firstOrCreate(['name' => 'super-admin']);
        $this->info('Super-admin role created/retrieved.');

        // Create the super admin user
        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@fitnezz.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Assign the super-admin role to the user
        $user->assignRole($role);

        // Give all permissions to the super-admin role
        $permissions = Permission::pluck('id')->all();
        $role->syncPermissions($permissions);

        $this->info('Super admin user created successfully!');
        $this->info('Email: admin@fitnezz.com');
        $this->info('Password: password');
    }
}
