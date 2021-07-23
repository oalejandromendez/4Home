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
        $role->givePermissionTo([
            'ACCEDER_RESERVAS',
            'VER_RESERVAS',
            'CREAR_RESERVAS',
            'MODIFICAR_RESERVAS',
            'ELIMINAR_RESERVAS',
            'ACCEDER_REPROGRAMACIONES',
            'VER_REPROGRAMACIONES',
            'CREAR_REPROGRAMACIONES',
            'ACCEDER_HISTORIAL_CLIENTE',
            'VER_TIPO_SERVICIO'
        ]);

        $role = Role::create(['name' => 'SERVICIO', 'guard_name' => 'api']);
        $role->givePermissionTo([
            'ACCEDER_RESERVAS',
            'VER_RESERVAS',
            'CREAR_RESERVAS',
            'MODIFICAR_RESERVAS',
            'ELIMINAR_RESERVAS',
            'ACCEDER_REPROGRAMACIONES',
            'VER_REPROGRAMACIONES',
            'CREAR_REPROGRAMACIONES',
            'ACCEDER_CLIENTES',
            'VER_CLIENTES',
            'ACCEDER_AGENDAMIENTOS',
            'VER_AGENDAMIENTOS',
            'MODIFICAR_AGENDAMIENTOS',
            'VER_TIPO_CLIENTE',
            'VER_TIPO_SERVICIO',
        ]);

        $role = Role::create(['name' => 'OPERACIÓN', 'guard_name' => 'api']);
        $role->givePermissionTo([
            'ACCEDER_USUARIOS',
            'VER_USUARIOS',
            'CREAR_USUARIOS',
            'MODIFICAR_USUARIOS',
            'ELIMINAR_USUARIOS',
            'ACCEDER_ROLES',
            'VER_ROLES',
            'CREAR_ROLES',
            'MODIFICAR_ROLES',
            'ELIMINAR_ROLES',
            'ACCEDER_PROFESIONALES',
            'VER_PROFESIONALES',
            'CREAR_PROFESIONALES',
            'MODIFICAR_PROFESIONALES',
            'ELIMINAR_PROFESIONALES',
            'ACCEDER_CARGOS',
            'VER_CARGOS',
            'CREAR_CARGOS',
            'MODIFICAR_CARGOS',
            'ELIMINAR_CARGOS',
            'ACCEDER_JORNADAS',
            'VER_JORNADAS',
            'CREAR_JORNADAS',
            'MODIFICAR_JORNADAS',
            'ELIMINAR_JORNADAS',
            'ACCEDER_SERVICIOS',
            'VER_SERVICIOS',
            'CREAR_SERVICIOS',
            'MODIFICAR_SERVICIOS',
            'ELIMINAR_SERVICIOS',
            'ACCEDER_TIPO_CLIENTE',
            'VER_TIPO_CLIENTE',
            'CREAR_TIPO_CLIENTE',
            'MODIFICAR_TIPO_CLIENTE',
            'ELIMINAR_TIPO_CLIENTE',
            'ACCEDER_CLIENTES',
            'VER_CLIENTES',
            'CREAR_CLIENTES',
            'MODIFICAR_CLIENTES',
            'ELIMINAR_CLIENTES',
            'ACCEDER_RESERVAS',
            'VER_RESERVAS',
            'CREAR_RESERVAS',
            'MODIFICAR_RESERVAS',
            'ELIMINAR_RESERVAS',
            'ACCEDER_TIPO_SERVICIO',
            'VER_TIPO_SERVICIO',
            'CREAR_TIPO_SERVICIO',
            'MODIFICAR_TIPO_SERVICIO',
            'ELIMINAR_TIPO_SERVICIO',
            'ACCEDER_ESTADOS',
            'VER_ESTADOS',
            'CREAR_ESTADOS',
            'MODIFICAR_ESTADOS',
            'ELIMINAR_ESTADOS',
            'ACCEDER_DISPONIBILIDAD',
            'VER_DISPONIBILIDAD',
            'MODIFICAR_DISPONIBILIDAD',
            'ACCEDER_CODIGOS_PROMOCIONALES',
            'VER_CODIGOS_PROMOCIONALES',
            'CREAR_CODIGOS_PROMOCIONALES',
            'MODIFICAR_CODIGOS_PROMOCIONALES',
            'ELIMINAR_CODIGOS_PROMOCIONALES',
            'ACCEDER_REPROGRAMACIONES',
            'VER_REPROGRAMACIONES',
            'CREAR_REPROGRAMACIONES',
            'ACCEDER_AGENDAMIENTOS',
            'VER_AGENDAMIENTOS',
            'MODIFICAR_AGENDAMIENTOS'
        ]);
    }
}
