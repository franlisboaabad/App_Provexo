<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{

    public function __construct()
    {
        $this->middleware('can:admin.roles.index')->only('index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::all();
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permisos = Permission::all();
        return view('admin.roles.create', compact('permisos'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permisos' => ['nullable', 'array'],
            'permisos.*' => ['exists:permissions,id'],
        ]);

        Log::info('Creando nuevo rol', ['name' => $validated['name']]);

        try {
            DB::transaction(function () use ($validated) {
                $role = Role::create(['name' => $validated['name']]);

                if (!empty($validated['permisos'])) {
                    $role->permissions()->sync($validated['permisos']);
                }

                Log::info('Rol creado exitosamente', [
                    'role_id' => $role->id,
                    'name' => $role->name,
                    'permissions_count' => $role->permissions->count()
                ]);
            });

            return back()->with('success', 'El rol fue creado correctamente');

        } catch (\Exception $e) {
            Log::error('Error al crear rol', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Error al crear el rol. Intente nuevamente.']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        // return view('admin.roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Role  $role)
    {
        //

        // dd($rol);
        $permisos = Permission::all();
        return view('admin.roles.edit', compact('role', 'permisos'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,' . $role->id],
            'permisos' => ['nullable', 'array'],
            'permisos.*' => ['exists:permissions,id'],
        ]);

        Log::info('Actualizando rol', [
            'role_id' => $role->id,
            'old_name' => $role->name,
            'new_name' => $validated['name']
        ]);

        try {
            DB::transaction(function () use ($role, $validated) {
                $role->name = $validated['name'];
                $role->save();

                $permisosIds = $validated['permisos'] ?? [];
                $role->permissions()->sync($permisosIds);

                Log::info('Rol actualizado exitosamente', [
                    'role_id' => $role->id,
                    'name' => $role->name,
                    'permissions_count' => count($permisosIds)
                ]);
            });

            return back()->with('success', 'Permisos del rol actualizados correctamente');

        } catch (\Exception $e) {
            Log::error('Error al actualizar rol', [
                'role_id' => $role->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Error al actualizar el rol. Intente nuevamente.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
