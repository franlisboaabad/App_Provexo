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
                'admin.roles.destroy' => 'Eliminar rol',
            ],
            'usuarios' => [
                'admin.usuarios.index' => 'Lista de usuarios',
                'admin.usuarios.edit' => 'Editar usuario',
                'admin.usuarios.update' => 'Actualizar usuario y asignar roles',
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
            ],
            'cotizaciones' => [
                'admin.cotizaciones.index' => 'Lista de cotizaciones',
                'admin.cotizaciones.create' => 'Crear cotización',
                'admin.cotizaciones.edit' => 'Editar cotización',
                'admin.cotizaciones.show' => 'Ver cotización',
                'admin.cotizaciones.destroy' => 'Eliminar cotización',
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
                'admin.clientes.index',
                'admin.clientes.show',
                'admin.cotizaciones.index',
                'admin.cotizaciones.create',
                'admin.cotizaciones.show',
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
