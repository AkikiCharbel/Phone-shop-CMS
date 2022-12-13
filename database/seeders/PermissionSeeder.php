<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'brand.view']);
        Permission::create(['name' => 'brand.create']);
        Permission::create(['name' => 'brand.list']);
        Permission::create(['name' => 'brand.delete']);
        Permission::create(['name' => 'brand.update']);

        Permission::create(['name' => 'brand-model.view']);
        Permission::create(['name' => 'brand-model.create']);
        Permission::create(['name' => 'brand-model.list']);
        Permission::create(['name' => 'brand-model.delete']);
        Permission::create(['name' => 'brand-model.update']);

        Permission::create(['name' => 'customer.view']);
        Permission::create(['name' => 'customer.create']);
        Permission::create(['name' => 'customer.list']);
        Permission::create(['name' => 'customer.delete']);
        Permission::create(['name' => 'customer.update']);

        Permission::create(['name' => 'phone.view']);
        Permission::create(['name' => 'phone.create']);
        Permission::create(['name' => 'phone.list']);
        Permission::create(['name' => 'phone.delete']);
        Permission::create(['name' => 'phone.update']);

        Permission::create(['name' => 'purchase.view']);
        Permission::create(['name' => 'purchase.create']);
        Permission::create(['name' => 'purchase.list']);
        Permission::create(['name' => 'purchase.delete']);
        Permission::create(['name' => 'purchase.update']);

        Permission::create(['name' => 'sellout.view']);
        Permission::create(['name' => 'sellout.create']);
        Permission::create(['name' => 'sellout.list']);
        Permission::create(['name' => 'sellout.delete']);
        Permission::create(['name' => 'sellout.update']);

        Permission::create(['name' => 'user-management.view']);
        Permission::create(['name' => 'user-management.create']);
        Permission::create(['name' => 'user-management.list']);
        Permission::create(['name' => 'user-management.delete']);
        Permission::create(['name' => 'user-management.update']);

        $role = Role::create(['name' => 'admin']);

        $role->givePermissionTo(Permission::all());

        $role = Role::create(['name' => 'employee']);
        $role->givePermissionTo([
            'sellout.view', 'sellout.list', 'sellout.delete', 'sellout.update', 'sellout.create',
            'phone.view', 'phone.list', 'customer.list', 'customer.view', 'customer.create', 'customer.update',
        ]);
        $role = Role::create(['name' => 'customer']);
    }
}
