<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // Create permissions for users
        $permissions = [
            // User permissions
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            // Role permissions
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',

            // Trade permissions
            'trades.view',
            'trades.create',
            'trades.edit',
            'trades.delete',

            // Daily limit permissions
            'daily_limits.view',
            'daily_limits.create',
            'daily_limits.edit',
            'daily_limits.delete',

            // Settings permissions
            'settings.view',
            'settings.edit',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission],
                ['guard_name' => 'web']
            );
        }

        // Create super-admin role
        $superAdmin = Role::firstOrCreate(
            ['name' => 'super-admin'],
            ['guard_name' => 'web']
        );
        $superAdmin->syncPermissions(Permission::all());

        // Create admin role (can manage users and roles, but has some restrictions)
        $admin = Role::firstOrCreate(
            ['name' => 'admin'],
            ['guard_name' => 'web']
        );
        $admin->syncPermissions(Permission::all());

        // Create trader role (only can manage their own trades)
        $trader = Role::firstOrCreate(
            ['name' => 'trader'],
            ['guard_name' => 'web']
        );
        $trader->syncPermissions([
            'trades.view',
            'trades.create',
            'trades.edit',
            'daily_limits.view',
            'settings.view',
            'settings.edit',
        ]);

        // Create viewer role (read-only)
        $viewer = Role::firstOrCreate(
            ['name' => 'viewer'],
            ['guard_name' => 'web']
        );
        $viewer->syncPermissions([
            'trades.view',
            'daily_limits.view',
            'settings.view',
        ]);
    }
}
