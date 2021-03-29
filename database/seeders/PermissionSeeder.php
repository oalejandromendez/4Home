<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');


        Permission::create(['name' => 'ACCEDER_USUARIOS', 'guard_name' => 'api']);
        Permission::create(['name' => 'VER_USUARIOS', 'guard_name' => 'api']);
        Permission::create(['name' => 'CREAR_USUARIOS', 'guard_name' => 'api']);
        Permission::create(['name' => 'MODIFICAR_USUARIOS', 'guard_name' => 'api']);
        Permission::create(['name' => 'ELIMINAR_USUARIOS', 'guard_name' => 'api']);
    }
}
