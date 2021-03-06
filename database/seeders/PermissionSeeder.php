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

        Permission::create(['name' => 'ACCEDER_ROLES', 'guard_name' => 'api']);
        Permission::create(['name' => 'VER_ROLES', 'guard_name' => 'api']);
        Permission::create(['name' => 'CREAR_ROLES', 'guard_name' => 'api']);
        Permission::create(['name' => 'MODIFICAR_ROLES', 'guard_name' => 'api']);
        Permission::create(['name' => 'ELIMINAR_ROLES', 'guard_name' => 'api']);

        Permission::create(['name' => 'ACCEDER_PROFESIONALES', 'guard_name' => 'api']);
        Permission::create(['name' => 'VER_PROFESIONALES', 'guard_name' => 'api']);
        Permission::create(['name' => 'CREAR_PROFESIONALES', 'guard_name' => 'api']);
        Permission::create(['name' => 'MODIFICAR_PROFESIONALES', 'guard_name' => 'api']);
        Permission::create(['name' => 'ELIMINAR_PROFESIONALES', 'guard_name' => 'api']);

        Permission::create(['name' => 'ACCEDER_CARGOS', 'guard_name' => 'api']);
        Permission::create(['name' => 'VER_CARGOS', 'guard_name' => 'api']);
        Permission::create(['name' => 'CREAR_CARGOS', 'guard_name' => 'api']);
        Permission::create(['name' => 'MODIFICAR_CARGOS', 'guard_name' => 'api']);
        Permission::create(['name' => 'ELIMINAR_CARGOS', 'guard_name' => 'api']);

        Permission::create(['name' => 'ACCEDER_JORNADAS', 'guard_name' => 'api']);
        Permission::create(['name' => 'VER_JORNADAS', 'guard_name' => 'api']);
        Permission::create(['name' => 'CREAR_JORNADAS', 'guard_name' => 'api']);
        Permission::create(['name' => 'MODIFICAR_JORNADAS', 'guard_name' => 'api']);
        Permission::create(['name' => 'ELIMINAR_JORNADAS', 'guard_name' => 'api']);

        Permission::create(['name' => 'ACCEDER_SERVICIOS', 'guard_name' => 'api']);
        Permission::create(['name' => 'VER_SERVICIOS', 'guard_name' => 'api']);
        Permission::create(['name' => 'CREAR_SERVICIOS', 'guard_name' => 'api']);
        Permission::create(['name' => 'MODIFICAR_SERVICIOS', 'guard_name' => 'api']);
        Permission::create(['name' => 'ELIMINAR_SERVICIOS', 'guard_name' => 'api']);

        Permission::create(['name' => 'ACCEDER_TIPO_CLIENTE', 'guard_name' => 'api']);
        Permission::create(['name' => 'VER_TIPO_CLIENTE', 'guard_name' => 'api']);
        Permission::create(['name' => 'CREAR_TIPO_CLIENTE', 'guard_name' => 'api']);
        Permission::create(['name' => 'MODIFICAR_TIPO_CLIENTE', 'guard_name' => 'api']);
        Permission::create(['name' => 'ELIMINAR_TIPO_CLIENTE', 'guard_name' => 'api']);

        Permission::create(['name' => 'ACCEDER_CLIENTES', 'guard_name' => 'api']);
        Permission::create(['name' => 'VER_CLIENTES', 'guard_name' => 'api']);
        Permission::create(['name' => 'CREAR_CLIENTES', 'guard_name' => 'api']);
        Permission::create(['name' => 'MODIFICAR_CLIENTES', 'guard_name' => 'api']);
        Permission::create(['name' => 'ELIMINAR_CLIENTES', 'guard_name' => 'api']);

        Permission::create(['name' => 'ACCEDER_FESTIVOS', 'guard_name' => 'api']);
        Permission::create(['name' => 'VER_FESTIVOS', 'guard_name' => 'api']);
        Permission::create(['name' => 'CREAR_FESTIVOS', 'guard_name' => 'api']);
        Permission::create(['name' => 'MODIFICAR_FESTIVOS', 'guard_name' => 'api']);
        Permission::create(['name' => 'ELIMINAR_FESTIVOS', 'guard_name' => 'api']);

        Permission::create(['name' => 'ACCEDER_RESERVAS', 'guard_name' => 'api']);
        Permission::create(['name' => 'VER_RESERVAS', 'guard_name' => 'api']);
        Permission::create(['name' => 'CREAR_RESERVAS', 'guard_name' => 'api']);
        Permission::create(['name' => 'MODIFICAR_RESERVAS', 'guard_name' => 'api']);
        Permission::create(['name' => 'ELIMINAR_RESERVAS', 'guard_name' => 'api']);

        Permission::create(['name' => 'ACCEDER_TIPO_SERVICIO', 'guard_name' => 'api']);
        Permission::create(['name' => 'VER_TIPO_SERVICIO', 'guard_name' => 'api']);
        Permission::create(['name' => 'CREAR_TIPO_SERVICIO', 'guard_name' => 'api']);
        Permission::create(['name' => 'MODIFICAR_TIPO_SERVICIO', 'guard_name' => 'api']);
        Permission::create(['name' => 'ELIMINAR_TIPO_SERVICIO', 'guard_name' => 'api']);

        Permission::create(['name' => 'ACCEDER_ESTADOS', 'guard_name' => 'api']);
        Permission::create(['name' => 'VER_ESTADOS', 'guard_name' => 'api']);
        Permission::create(['name' => 'CREAR_ESTADOS', 'guard_name' => 'api']);
        Permission::create(['name' => 'MODIFICAR_ESTADOS', 'guard_name' => 'api']);
        Permission::create(['name' => 'ELIMINAR_ESTADOS', 'guard_name' => 'api']);

        Permission::create(['name' => 'ACCEDER_AGENDAMIENTOS', 'guard_name' => 'api']);
        Permission::create(['name' => 'VER_AGENDAMIENTOS', 'guard_name' => 'api']);
        Permission::create(['name' => 'MODIFICAR_AGENDAMIENTOS', 'guard_name' => 'api']);

        Permission::create(['name' => 'ACCEDER_DISPONIBILIDAD', 'guard_name' => 'api']);
        Permission::create(['name' => 'VER_DISPONIBILIDAD', 'guard_name' => 'api']);
        Permission::create(['name' => 'MODIFICAR_DISPONIBILIDAD', 'guard_name' => 'api']);

        Permission::create(['name' => 'ACCEDER_CODIGOS_PROMOCIONALES', 'guard_name' => 'api']);
        Permission::create(['name' => 'VER_CODIGOS_PROMOCIONALES', 'guard_name' => 'api']);
        Permission::create(['name' => 'CREAR_CODIGOS_PROMOCIONALES', 'guard_name' => 'api']);
        Permission::create(['name' => 'MODIFICAR_CODIGOS_PROMOCIONALES', 'guard_name' => 'api']);
        Permission::create(['name' => 'ELIMINAR_CODIGOS_PROMOCIONALES', 'guard_name' => 'api']);

        Permission::create(['name' => 'ACCEDER_REPROGRAMACIONES', 'guard_name' => 'api']);
        Permission::create(['name' => 'VER_REPROGRAMACIONES', 'guard_name' => 'api']);
        Permission::create(['name' => 'CREAR_REPROGRAMACIONES', 'guard_name' => 'api']);

        Permission::create(['name' => 'ACCEDER_REPORTES', 'guard_name' => 'api']);
        Permission::create(['name' => 'REPORTE_AGENDA', 'guard_name' => 'api']);
        Permission::create(['name' => 'REPORTE_PAGOS_VENCIDOS', 'guard_name' => 'api']);
        Permission::create(['name' => 'REPORTE_HISTORIAL', 'guard_name' => 'api']);
        Permission::create(['name' => 'REPORTE_PAGOS_PENDIENTES', 'guard_name' => 'api']);
        Permission::create(['name' => 'REPORTE_SERVICIOS_PROFESIONALES', 'guard_name' => 'api']);
        Permission::create(['name' => 'REPORTE_HISTORIAL_PAGOS', 'guard_name' => 'api']);
        Permission::create(['name' => 'REPORTE_SERVICIO', 'guard_name' => 'api']);
        Permission::create(['name' => 'REPORTE_REGISTRO_ACTIVIDADES', 'guard_name' => 'api']);

        Permission::create(['name' => 'ACCEDER_HISTORIAL_CLIENTE', 'guard_name' => 'api']);
    }
}
