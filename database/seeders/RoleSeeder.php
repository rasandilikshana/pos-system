<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'products.view',
            'products.manage',
            'categories.view',
            'categories.manage',
            'suppliers.view',
            'suppliers.manage',
            'customers.view',
            'customers.manage',
            'sales.create',
            'sales.view.own',
            'sales.view.all',
            'sales.void',
            'stock.adjust',
            'reports.view',
            'users.manage',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web'])
            ->syncPermissions($permissions);

        Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web'])
            ->syncPermissions(array_values(array_diff($permissions, ['users.manage'])));

        Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web'])
            ->syncPermissions([
                'products.view',
                'categories.view',
                'customers.view',
                'customers.manage',
                'sales.create',
                'sales.view.own',
            ]);
    }
}
