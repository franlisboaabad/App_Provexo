<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Define los permisos del sistema agrupados por módulo
     */
    private function getPermissions(): array
    {
        return [
            'dashboard' => [
                'admin.home' => 'Ver Dashboard',
            ],
            'roles' => [
                'admin.roles.index' => 'Lista de roles',
                'admin.roles.create' => 'Registrar rol',
                'admin.roles.edit' => 'Editar rol',
                'admin.roles.show' => 'Ver rol',
                'admin.roles.destroy' => 'Eliminar rol',
            ],
            'usuarios' => [
                'admin.usuarios.index' => 'Lista de usuarios',
                'admin.usuarios.create' => 'Crear usuario',
                'admin.usuarios.edit' => 'Editar usuario',
                'admin.usuarios.show' => 'Ver usuario',
                'admin.usuarios.update' => 'Actualizar usuario y asignar roles',
                'admin.usuarios.destroy' => 'Eliminar usuario',
            ],
            'proveedores' => [
                'admin.proveedores.index' => 'Lista de proveedores',
                'admin.proveedores.create' => 'Registrar proveedor',
                'admin.proveedores.edit' => 'Editar proveedor',
                'admin.proveedores.show' => 'Ver proveedor',
                'admin.proveedores.destroy' => 'Eliminar proveedor',
            ],
            'clientes' => [
                'admin.clientes.index' => 'Lista de clientes',
                'admin.clientes.create' => 'Registrar cliente',
                'admin.clientes.edit' => 'Editar cliente',
                'admin.clientes.show' => 'Ver cliente',
                'admin.clientes.destroy' => 'Eliminar cliente',
            ],
            'productos' => [
                'admin.productos.index' => 'Lista de productos',
                'admin.productos.create' => 'Registrar producto',
                'admin.productos.edit' => 'Editar producto',
                'admin.productos.show' => 'Ver producto',
                'admin.productos.destroy' => 'Eliminar producto',
                'admin.productos.import' => 'Importar productos',
            ],
            'cotizaciones' => [
                'admin.cotizaciones.index' => 'Lista de cotizaciones',
                'admin.cotizaciones.create' => 'Crear cotización',
                'admin.cotizaciones.edit' => 'Editar cotización',
                'admin.cotizaciones.show' => 'Ver cotización',
                'admin.cotizaciones.destroy' => 'Eliminar cotización',
                'admin.cotizaciones.pdf' => 'Ver PDF de cotización',
                'admin.cotizaciones.publica' => 'Generar URL pública de cotización',
                'admin.cotizaciones.enviar-email' => 'Enviar cotización por email',
                'admin.cotizaciones.venta' => 'Ver información de venta de cotización',
            ],
            'ventas' => [
                'admin.ventas.index' => 'Lista de ventas',
                'admin.ventas.create' => 'Crear venta',
                'admin.ventas.store' => 'Guardar venta',
                'admin.ventas.show' => 'Ver venta',
                'admin.ventas.edit' => 'Editar venta',
                'admin.ventas.update' => 'Actualizar venta',
                'admin.ventas.destroy' => 'Eliminar venta',
                'admin.ventas.actualizar-estado-pedido' => 'Actualizar estado de pedido',
            ],
            'empresas' => [
                'admin.empresas.index' => 'Lista de empresas',
                'admin.empresas.create' => 'Crear empresa',
                'admin.empresas.edit' => 'Editar empresa',
                'admin.empresas.show' => 'Ver empresa',
                'admin.empresas.destroy' => 'Eliminar empresa',
            ],
            'series_cotizacion' => [
                'admin.series.store' => 'Crear serie de cotización',
                'admin.series.update' => 'Actualizar serie de cotización',
                'admin.series.destroy' => 'Eliminar serie de cotización',
            ],
            'cuentas_bancarias' => [
                'admin.cuentas.store' => 'Crear cuenta bancaria',
                'admin.cuentas.update' => 'Actualizar cuenta bancaria',
                'admin.cuentas.destroy' => 'Eliminar cuenta bancaria',
            ],
            'documentos_clientes' => [
                'admin.documentos-clientes.index' => 'Lista de documentos',
                'admin.documentos-clientes.create' => 'Crear documento',
                'admin.documentos-clientes.edit' => 'Editar documento',
                'admin.documentos-clientes.show' => 'Ver documento',
                'admin.documentos-clientes.destroy' => 'Eliminar documento',
            ],
            'configuracion_documentos' => [
                'admin.configuracion-documentos.edit' => 'Editar configuración de documentos',
                'admin.configuracion-documentos.update' => 'Actualizar configuración de documentos',
            ],
        ];
    }

    /**
     * Define los roles y sus permisos asociados
     */
    private function getRolePermissions(): array
    {
        return [
            'Admin' => ['*'], // El admin tiene acceso a todo
            'Proveedor' => [
                'admin.home',
                'admin.proveedores.index',
                'admin.proveedores.show',
                'admin.productos.index',
                'admin.productos.create',
                'admin.productos.edit',
                'admin.productos.show',
            ],
            'Cliente' => [
                'admin.home',
                'admin.cotizaciones.index',
                'admin.cotizaciones.show',
                'admin.ventas.index',
                'admin.ventas.show',
                'admin.documentos-clientes.index',
                'admin.documentos-clientes.show',
            ],
        ];
    }

    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Crear roles
        $roles = [];
        foreach ($this->getRolePermissions() as $roleName => $permissions) {
            $roles[$roleName] = Role::firstOrCreate(['name' => $roleName]);
        }

        // Crear y sincronizar permisos
        foreach ($this->getPermissions() as $module => $modulePermissions) {
            foreach ($modulePermissions as $permissionName => $description) {
                $permission = Permission::firstOrCreate(
                    ['name' => $permissionName],
                    ['description' => $description]
                );

                // Asignar permisos a roles
                foreach ($this->getRolePermissions() as $roleName => $rolePermissions) {
                    if (in_array('*', $rolePermissions) || in_array($permissionName, $rolePermissions)) {
                        $roles[$roleName]->givePermissionTo($permission);
                    }
                }
            }
        }
    }
}
