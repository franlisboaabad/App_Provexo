<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProveedorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:admin.proveedores.index')->only('index');
        $this->middleware('can:admin.proveedores.create')->only('create', 'store');
        $this->middleware('can:admin.proveedores.edit')->only('edit', 'update');
        $this->middleware('can:admin.proveedores.show')->only('show');
        $this->middleware('can:admin.proveedores.destroy')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Log::info('Listando proveedores');

        $proveedores = Proveedor::with('user')->get();

        return view('admin.proveedores.index', compact('proveedores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.proveedores.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'celular' => ['nullable', 'string', 'max:20'],
            'empresa' => ['nullable', 'string', 'max:255'],
            'ruc' => ['nullable', 'string', 'max:100'],
        ]);

        Log::info('Creando nuevo proveedor', ['email' => $request->email]);

        try {
            DB::transaction(function () use ($validated) {
                $user = \App\Models\User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
                    'activo' => true,
                ]);

                $user->assignRole('Proveedor');

                Proveedor::create([
                    'user_id' => $user->id,
                    'celular' => $validated['celular'] ?? null,
                    'empresa' => $validated['empresa'] ?? null,
                    'ruc' => $validated['ruc'] ?? null,
                ]);

                Log::info('Proveedor creado exitosamente', ['user_id' => $user->id]);
            });

            return redirect()->route('admin.proveedores.index')
                ->with('success', 'Proveedor creado exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al crear proveedor', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Error al crear proveedor. Intente nuevamente.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Proveedor $proveedore)
    {
        $proveedore->load('user');
        return view('admin.proveedores.show', compact('proveedore'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Proveedor $proveedore)
    {
        $proveedore->load('user');
        return view('admin.proveedores.edit', compact('proveedore'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Proveedor $proveedore)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $proveedore->user_id],
            'celular' => ['nullable', 'string', 'max:20'],
            'empresa' => ['nullable', 'string', 'max:255'],
            'ruc' => ['nullable', 'string', 'max:100'],
        ]);

        Log::info('Actualizando proveedor', ['proveedor_id' => $proveedore->id]);

        try {
            DB::transaction(function () use ($validated, $proveedore) {
                $proveedore->user->update([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                ]);

                $proveedore->update([
                    'celular' => $validated['celular'] ?? null,
                    'empresa' => $validated['empresa'] ?? null,
                    'ruc' => $validated['ruc'] ?? null,
                ]);

                Log::info('Proveedor actualizado exitosamente', ['proveedor_id' => $proveedore->id]);
            });

            return redirect()->route('admin.proveedores.index')
                ->with('success', 'Proveedor actualizado exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al actualizar proveedor', [
                'proveedor_id' => $proveedore->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Error al actualizar proveedor. Intente nuevamente.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Proveedor $proveedore)
    {
        Log::info('Eliminando proveedor', ['proveedor_id' => $proveedore->id]);

        try {
            DB::transaction(function () use ($proveedore) {
                $user = $proveedore->user;
                $proveedore->delete();
                $user->delete();

                Log::info('Proveedor eliminado exitosamente', ['proveedor_id' => $proveedore->id]);
            });

            return redirect()->route('admin.proveedores.index')
                ->with('success', 'Proveedor eliminado exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al eliminar proveedor', [
                'proveedor_id' => $proveedore->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withErrors(['error' => 'Error al eliminar proveedor. Intente nuevamente.']);
        }
    }
}
