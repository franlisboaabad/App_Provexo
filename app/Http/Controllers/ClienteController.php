<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:admin.clientes.index')->only('index');
        $this->middleware('can:admin.clientes.create')->only('create', 'store');
        $this->middleware('can:admin.clientes.edit')->only('edit', 'update');
        $this->middleware('can:admin.clientes.show')->only('show');
        $this->middleware('can:admin.clientes.destroy')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Log::info('Listando clientes');

        $clientes = Cliente::with('user')->get();

        return view('admin.clientes.index', compact('clientes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.clientes.create');
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

        Log::info('Creando nuevo cliente', ['email' => $request->email]);

        try {
            $user = null;
            $cliente = null;

            DB::transaction(function () use ($validated, &$user, &$cliente) {
                $user = \App\Models\User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
                    'activo' => true,
                ]);

                $user->assignRole('Cliente');

                $cliente = Cliente::create([
                    'user_id' => $user->id,
                    'celular' => $validated['celular'] ?? null,
                    'empresa' => $validated['empresa'] ?? null,
                    'ruc' => $validated['ruc'] ?? null,
                ]);

                Log::info('Cliente creado exitosamente', ['user_id' => $user->id]);
            });

            // Si es una petición AJAX, responder con JSON
            if (($request->expectsJson() || $request->ajax()) && $user && $cliente) {
                // Recargar el cliente con la relación de usuario para obtener datos actualizados
                $cliente->load('user');

                return response()->json([
                    'success' => true,
                    'message' => 'Cliente creado exitosamente',
                    'cliente' => [
                        'id' => $cliente->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'empresa' => $cliente->empresa ?? 'Sin empresa',
                    ]
                ]);
            }

            return redirect()->route('admin.clientes.index')
                ->with('success', 'Cliente creado exitosamente');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Si es una petición AJAX, responder con JSON de error de validación
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error al crear cliente', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Si es una petición AJAX, responder con JSON de error
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear cliente. Intente nuevamente.'
                ], 500);
            }

            return back()
                ->withInput()
                ->withErrors(['error' => 'Error al crear cliente. Intente nuevamente.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Cliente $cliente)
    {
        $cliente->load('user');
        $cotizaciones = $cliente->cotizaciones()
            ->with('productos.producto')
            ->orderBy('fecha_emision', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.clientes.show', compact('cliente', 'cotizaciones'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cliente $cliente)
    {
        $cliente->load('user');
        return view('admin.clientes.edit', compact('cliente'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cliente $cliente)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $cliente->user_id],
            'celular' => ['nullable', 'string', 'max:20'],
            'empresa' => ['nullable', 'string', 'max:255'],
            'ruc' => ['nullable', 'string', 'max:100'],
        ]);

        Log::info('Actualizando cliente', ['cliente_id' => $cliente->id]);

        try {
            DB::transaction(function () use ($validated, $cliente) {
                $cliente->user->update([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                ]);

                $cliente->update([
                    'celular' => $validated['celular'] ?? null,
                    'empresa' => $validated['empresa'] ?? null,
                    'ruc' => $validated['ruc'] ?? null,
                ]);

                Log::info('Cliente actualizado exitosamente', ['cliente_id' => $cliente->id]);
            });

            return redirect()->route('admin.clientes.index')
                ->with('success', 'Cliente actualizado exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al actualizar cliente', [
                'cliente_id' => $cliente->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Error al actualizar cliente. Intente nuevamente.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
    {
        Log::info('Eliminando cliente', ['cliente_id' => $cliente->id]);

        try {
            DB::transaction(function () use ($cliente) {
                $user = $cliente->user;
                $cliente->delete();
                $user->delete();

                Log::info('Cliente eliminado exitosamente', ['cliente_id' => $cliente->id]);
            });

            return redirect()->route('admin.clientes.index')
                ->with('success', 'Cliente eliminado exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al eliminar cliente', [
                'cliente_id' => $cliente->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withErrors(['error' => 'Error al eliminar cliente. Intente nuevamente.']);
        }
    }
}
