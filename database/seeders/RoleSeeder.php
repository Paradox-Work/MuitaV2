<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Clear cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions using firstOrCreate (won't error if exists)
        $permissions = [
            'view cases',
            'create cases', 
            'edit cases',
            'delete cases',
            'assign inspections',
            'view reports',
            'manage users'
        ];
        
        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web'
            ]);
        }

        // Create roles using firstOrCreate
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $inspectorRole = Role::firstOrCreate(['name' => 'inspector', 'guard_name' => 'web']);
        $brokerRole = Role::firstOrCreate(['name' => 'broker', 'guard_name' => 'web']);
        $analystRole = Role::firstOrCreate(['name' => 'analyst', 'guard_name' => 'web']);

        // Assign permissions to roles (syncPermissions replaces all permissions)
        $adminRole->syncPermissions(Permission::all());
        $inspectorRole->syncPermissions(['view cases', 'edit cases', 'assign inspections']);
        $brokerRole->syncPermissions(['view cases', 'create cases']);
        $analystRole->syncPermissions(['view cases', 'view reports']);

        // Create users (only if they don't exist)
        if (!User::where('email', 'admin@system.com')->exists()) {
            $adminUser = User::create([
                'name' => 'Admin User',
                'email' => 'admin@system.com',
                'password' => Hash::make('admin123'),
            ]);
            $adminUser->assignRole($adminRole);
        }

        if (!User::where('email', 'inspector@system.com')->exists()) {
            $inspectorUser = User::create([
                'name' => 'John Inspector',
                'email' => 'inspector@system.com',
                'password' => Hash::make('insp123'),
            ]);
            $inspectorUser->assignRole($inspectorRole);
        }

        if (!User::where('email', 'broker@system.com')->exists()) {
            $brokerUser = User::create([
                'name' => 'Jane Broker',
                'email' => 'broker@system.com',
                'password' => Hash::make('broker123'),
            ]);
            $brokerUser->assignRole($brokerRole);
        }

        if (!User::where('email', 'analyst@system.com')->exists()) {
            $analystUser = User::create([
                'name' => 'Mike Analyst',
                'email' => 'analyst@system.com',
                'password' => Hash::make('analyst123'),
            ]);
            $analystUser->assignRole($analystRole);
        }
    }
}