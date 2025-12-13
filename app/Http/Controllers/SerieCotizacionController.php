<?php

namespace App\Http\Controllers;

use App\Models\SerieCotizacion;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SerieCotizacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:admin.series.store')->only('store');
        $this->middleware('can:admin.series.update')->only('update');
        $this->middleware('can:admin.series.destroy')->only('destroy');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Empresa $empresa)
    {
        $validated = $request->validate([
            'serie' => ['required', 'string', 'max:10'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'activa' => ['nullable', 'boolean'],
            'es_principal' => ['nullable', 'boolean'],
        ]);

        // Validar que la serie sea única para la empresa
        $request->validate([
            'serie' => ['unique:series_cotizacion,serie,NULL,id,empresa_id,' . $empresa->id],
        ], [
            'serie.unique' => 'Esta serie ya existe para esta empresa.',
        ]);

        Log::info('Creando nueva serie de cotización', ['empresa_id' => $empresa->id, 'serie' => $validated['serie']]);

        try {
            DB::transaction(function () use ($validated, $request, $empresa) {
                // Si se marca como principal, desmarcar las demás de la misma empresa
                if ($request->has('es_principal') && $request->es_principal) {
                    SerieCotizacion::where('empresa_id', $empresa->id)
                        ->where('es_principal', true)
                        ->update(['es_principal' => false]);
                }

                SerieCotizacion::create([
                    'empresa_id' => $empresa->id,
                    'serie' => strtoupper($validated['serie']),
                    'descripcion' => $validated['descripcion'] ?? null,
                    'activa' => $request->has('activa') && $request->activa ? true : ($validated['activa'] ?? true),
                    'es_principal' => $request->has('es_principal') && $request->es_principal ? true : false,
                    'correlativo_inicial' => $validated['correlativo_inicial'] ?? 1,
                ]);

                Log::info('Serie de cotización creada exitosamente', ['serie' => $validated['serie']]);
            });

            return back()->with('success', 'Serie de cotización creada exitosamente');

        } catch (ValidationException $e) {
            Log::warning('Validation failed during serie creation', ['errors' => $e->errors()]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error al crear serie de cotización', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Error al crear serie de cotización. Intente nuevamente.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SerieCotizacion $serieCotizacion)
    {
        $validated = $request->validate([
            'serie' => ['required', 'string', 'max:10'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'activa' => ['nullable', 'boolean'],
            'es_principal' => ['nullable', 'boolean'],
            'correlativo_inicial' => ['nullable', 'integer', 'min:1'],
        ]);

        // Validar que la serie sea única para la empresa (excluyendo la actual)
        $request->validate([
            'serie' => ['unique:series_cotizacion,serie,' . $serieCotizacion->id . ',id,empresa_id,' . $serieCotizacion->empresa_id],
        ], [
            'serie.unique' => 'Esta serie ya existe para esta empresa.',
        ]);

        Log::info('Actualizando serie de cotización', ['serie_id' => $serieCotizacion->id]);

        try {
            DB::transaction(function () use ($validated, $request, $serieCotizacion) {
                // Si se marca como principal, desmarcar las demás de la misma empresa
                if ($request->has('es_principal') && $request->es_principal) {
                    SerieCotizacion::where('empresa_id', $serieCotizacion->empresa_id)
                        ->where('id', '!=', $serieCotizacion->id)
                        ->where('es_principal', true)
                        ->update(['es_principal' => false]);
                }

                $serieCotizacion->update([
                    'serie' => strtoupper($validated['serie']),
                    'descripcion' => $validated['descripcion'] ?? null,
                    'activa' => $request->has('activa') && $request->activa ? true : false,
                    'es_principal' => $request->has('es_principal') && $request->es_principal ? true : false,
                    'correlativo_inicial' => $validated['correlativo_inicial'] ?? $serieCotizacion->correlativo_inicial ?? 1,
                ]);

                Log::info('Serie de cotización actualizada exitosamente', ['serie_id' => $serieCotizacion->id]);
            });

            return back()->with('success', 'Serie de cotización actualizada exitosamente');

        } catch (ValidationException $e) {
            Log::warning('Validation failed during serie update', ['serie_id' => $serieCotizacion->id, 'errors' => $e->errors()]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error al actualizar serie de cotización', [
                'serie_id' => $serieCotizacion->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Error al actualizar serie de cotización. Intente nuevamente.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SerieCotizacion $serieCotizacion)
    {
        Log::info('Eliminando serie de cotización', ['serie_id' => $serieCotizacion->id]);

        try {
            DB::transaction(function () use ($serieCotizacion) {
                $serieCotizacion->delete();
                Log::info('Serie de cotización eliminada exitosamente', ['serie_id' => $serieCotizacion->id]);
            });

            return back()->with('success', 'Serie de cotización eliminada exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al eliminar serie de cotización', [
                'serie_id' => $serieCotizacion->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withErrors(['error' => 'Error al eliminar serie de cotización. Intente nuevamente.']);
        }
    }
}
