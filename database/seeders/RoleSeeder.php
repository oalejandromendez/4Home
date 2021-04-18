<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Crear rol y asignar permisos superadmin
        $role = Role::create(['name' => 'SUPERADMIN', 'guard_name' => 'api']);
        $role->givePermissionTo(Permission::all());

        $role = Role::create(['name' => 'CLIENTE', 'guard_name' => 'api']);
        $role->givePermissionTo(Permission::all());
    }
}
