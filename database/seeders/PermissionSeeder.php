<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'service.create',
            'service.update',
            'service.delete',
            'role.view',
            'role.update',
            'category.view',
            'category.create',
            'category.store',
            'category.edit',
            'category.update',
            'category.delete',
            'supplier.view',
            'supplier.create',
            'supplier.store',
            'supplier.edit',
            'supplier.update',
            'supplier.delete',
            'customer.view',
            'customer.create',
            'customer.store',
            'customer.edit',
            'customer.update',
            'customer.delete',
            'user.view',
            'user.create',
            'user.store',
            'user.edit',
            'user.update',
            'user.delete',
            'sparepart.view',
            'sparepart.create',
            'sparepart.store',
            'sparepart.edit',
            'sparepart.update',
            'sparepart.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $adminRole = Role::where('name', 'admin')->first();

        if ($adminRole) {
            $adminRole->syncPermissions($permissions);
        }
    }
}
