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
            'service.store',
            'service.view',
            'service.create',
            'service.update',
            'service.edit',
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
            'transaction.view',
            'transaction.create',
            'transaction.store',
            'transaction.edit',
            'transaction.update',
            'transaction.delete',
            'jenis-kendaraan.view',
            'jenis-kendaraan.create',
            'jenis-kendaraan.store',
            'jenis-kendaraan.update',
            'jenis-kendaraan.delete',
            // Permissions untuk Stock Handle
            'stock-handle.view',
            'stock-handle.create',
            'stock-handle.store',
            'stock-handle.edit',
            'stock-handle.update',
            'stock-handle.delete',
            'stock-handle.quick-create-sparepart',
            'report.transaction',
            'report.sparepart-report', 
            'report.inventory-summary',
            'report.purchase',
            'purchase_order.view',
            'purchase_order.create',
            'purchase_order.store',
            'purchase_order.edit',
            'purchase_order.update',
            'purchase_order.delete',
            'purchase_order_item.view',
            'purchase_order_item.create',
            'purchase_order_item.store',
            'purchase_order_item.edit',
            'purchase_order_item.update',
            'purchase_order_item.delete',
            'report.stock',
            'report.purchase',
            'manual-book-kasir.view',
            'manual-book-admin.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Beri semua permission ke admin
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions($permissions);

        // Beri permission tertentu ke kasir
        $kasirRole = Role::firstOrCreate(['name' => 'kasir']);
        $kasirPermissions = [
            'service.view',
            'category.view',
            'supplier.view',
            'customer.view',
            'user.view',
            'sparepart.view',
            'jenis-kendaraan.view',
            'stock-handle.view',
            'transaction.view',
            'transaction.create',
            'transaction.store',
            'transaction.edit',
            'transaction.update',
            'transaction.delete',
            'report.transaction',
             
            'report.purchase',
            'report.sparepart-report',
            'report.inventory-summary',
            'purchase_order.view',
            'purchase_order_item.view',
            'manual-book-kasir.view',
        ];
        $kasirRole->syncPermissions($kasirPermissions);
    }
}