<?php

namespace App\Http\Controllers;

use App\Models\CuentaBancaria;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CuentaBancariaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Empresa $empresa)
    {
        $validated = $request->validate([
            'banco' => ['required', 'string', 'max:255'],
            'tipo_cuenta' => ['nullable', 'string', 'max:50'],
            'numero_cuenta' => ['required', 'string', 'max:50'],
            'numero_cuenta_interbancario' => ['nullable', 'string', 'max:50'],
            'moneda_cuenta' => ['nullable', 'string', 'max:10'],
            'activa' => ['nullable', 'boolean'],
            'es_principal' => ['nullable', 'boolean'],
        ]);

        Log::info('Creando nueva cuenta bancaria', ['empresa_id' => $empresa->id, 'banco' => $validated['banco']]);

        try {
            DB::transaction(function () use ($validated, $request, $empresa) {
                // Si se marca como principal, desmarcar las demás de la misma empresa
                if ($request->has('es_principal') && $request->es_principal) {
                    CuentaBancaria::where('empresa_id', $empresa->id)
                        ->where('es_principal', true)
                        ->update(['es_principal' => false]);
                }

                CuentaBancaria::create([
                    'empresa_id' => $empresa->id,
                    'banco' => $validated['banco'],
                    'tipo_cuenta' => $validated['tipo_cuenta'] ?? null,
                    'numero_cuenta' => $validated['numero_cuenta'],
                    'numero_cuenta_interbancario' => $validated['numero_cuenta_interbancario'] ?? null,
                    'moneda_cuenta' => $validated['moneda_cuenta'] ?? 'PEN',
                    'activa' => $request->has('activa') && $request->activa ? true : ($validated['activa'] ?? true),
                    'es_principal' => $request->has('es_principal') && $request->es_principal ? true : false,
                ]);

                Log::info('Cuenta bancaria creada exitosamente', ['banco' => $validated['banco']]);
            });

            return back()->with('success', 'Cuenta bancaria creada exitosamente');

        } catch (ValidationException $e) {
            Log::warning('Validation failed during cuenta bancaria creation', ['errors' => $e->errors()]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error al crear cuenta bancaria', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Error al crear cuenta bancaria. Intente nuevamente.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CuentaBancaria $cuentaBancaria)
    {
        $validated = $request->validate([
            'banco' => ['required', 'string', 'max:255'],
            'tipo_cuenta' => ['nullable', 'string', 'max:50'],
            'numero_cuenta' => ['required', 'string', 'max:50'],
            'numero_cuenta_interbancario' => ['nullable', 'string', 'max:50'],
            'moneda_cuenta' => ['nullable', 'string', 'max:10'],
            'activa' => ['nullable', 'boolean'],
            'es_principal' => ['nullable', 'boolean'],
        ]);

        Log::info('Actualizando cuenta bancaria', ['cuenta_id' => $cuentaBancaria->id]);

        try {
            DB::transaction(function () use ($validated, $request, $cuentaBancaria) {
                // Si se marca como principal, desmarcar las demás de la misma empresa
                if ($request->has('es_principal') && $request->es_principal) {
                    CuentaBancaria::where('empresa_id', $cuentaBancaria->empresa_id)
                        ->where('id', '!=', $cuentaBancaria->id)
                        ->where('es_principal', true)
                        ->update(['es_principal' => false]);
                }

                $cuentaBancaria->update([
                    'banco' => $validated['banco'],
                    'tipo_cuenta' => $validated['tipo_cuenta'] ?? null,
                    'numero_cuenta' => $validated['numero_cuenta'],
                    'numero_cuenta_interbancario' => $validated['numero_cuenta_interbancario'] ?? null,
                    'moneda_cuenta' => $validated['moneda_cuenta'] ?? 'PEN',
                    'activa' => $request->has('activa') && $request->activa ? true : false,
                    'es_principal' => $request->has('es_principal') && $request->es_principal ? true : false,
                ]);

                Log::info('Cuenta bancaria actualizada exitosamente', ['cuenta_id' => $cuentaBancaria->id]);
            });

            return back()->with('success', 'Cuenta bancaria actualizada exitosamente');

        } catch (ValidationException $e) {
            Log::warning('Validation failed during cuenta bancaria update', ['cuenta_id' => $cuentaBancaria->id, 'errors' => $e->errors()]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error al actualizar cuenta bancaria', [
                'cuenta_id' => $cuentaBancaria->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Error al actualizar cuenta bancaria. Intente nuevamente.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CuentaBancaria $cuentaBancaria)
    {
        Log::info('Eliminando cuenta bancaria', ['cuenta_id' => $cuentaBancaria->id]);

        try {
            DB::transaction(function () use ($cuentaBancaria) {
                $cuentaBancaria->delete();
                Log::info('Cuenta bancaria eliminada exitosamente', ['cuenta_id' => $cuentaBancaria->id]);
            });

            return back()->with('success', 'Cuenta bancaria eliminada exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al eliminar cuenta bancaria', [
                'cuenta_id' => $cuentaBancaria->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withErrors(['error' => 'Error al eliminar cuenta bancaria. Intente nuevamente.']);
        }
    }
}
