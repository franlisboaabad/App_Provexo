<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class EmpresaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:admin.empresas.index')->only('index');
        $this->middleware('can:admin.empresas.create')->only('create', 'store');
        $this->middleware('can:admin.empresas.edit')->only('edit', 'update');
        $this->middleware('can:admin.empresas.show')->only('show');
        $this->middleware('can:admin.empresas.destroy')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Log::info('Listando empresas');

        $empresas = Empresa::latest()->get();

        return view('admin.empresas.index', compact('empresas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.empresas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'razon_social' => ['required', 'string', 'max:255'],
            'nombre_comercial' => ['nullable', 'string', 'max:255'],
            'ruc' => ['required', 'string', 'max:20', 'unique:empresas,ruc'],
            'direccion' => ['nullable', 'string', 'max:500'],
            'ciudad' => ['nullable', 'string', 'max:100'],
            'provincia' => ['nullable', 'string', 'max:100'],
            'distrito' => ['nullable', 'string', 'max:100'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'celular' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'web' => ['nullable', 'url', 'max:255'],
            'representante_legal' => ['nullable', 'string', 'max:255'],
            'tipo_empresa' => ['nullable', 'string', 'max:50'],
            'descripcion' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'activo' => ['nullable', 'boolean'],
            'es_principal' => ['nullable', 'boolean'],
        ]);

        Log::info('Creando nueva empresa', ['ruc' => $validated['ruc']]);

        try {
            DB::transaction(function () use ($validated, $request) {
                // Manejar subida de logo
                if ($request->hasFile('logo')) {
                    $logoPath = $request->file('logo')->store('empresas/logos', 'public');
                    $validated['logo'] = $logoPath;
                }

                // Si se marca como principal, desmarcar las demás
                if ($request->has('es_principal') && $request->es_principal) {
                    Empresa::where('es_principal', true)->update(['es_principal' => false]);
                }

                Empresa::create($validated);

                Log::info('Empresa creada exitosamente', ['ruc' => $validated['ruc']]);
            });

            return redirect()->route('admin.empresas.index')
                ->with('success', 'Empresa creada exitosamente');

        } catch (ValidationException $e) {
            Log::warning('Validation failed during empresa creation', ['errors' => $e->errors()]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error al crear empresa', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Error al crear empresa. Intente nuevamente.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Empresa $empresa)
    {
        Log::info('Mostrando empresa', ['empresa_id' => $empresa->id]);

        $empresa->load('seriesCotizacion', 'cuentasBancarias');

        return view('admin.empresas.show', compact('empresa'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Empresa $empresa)
    {
        Log::info('Editando empresa', ['empresa_id' => $empresa->id]);

        $empresa->load('seriesCotizacion', 'cuentasBancarias');

        return view('admin.empresas.edit', compact('empresa'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Empresa $empresa)
    {
        $validated = $request->validate([
            'razon_social' => ['required', 'string', 'max:255'],
            'nombre_comercial' => ['nullable', 'string', 'max:255'],
            'ruc' => ['required', 'string', 'max:20', 'unique:empresas,ruc,' . $empresa->id],
            'direccion' => ['nullable', 'string', 'max:500'],
            'ciudad' => ['nullable', 'string', 'max:100'],
            'provincia' => ['nullable', 'string', 'max:100'],
            'distrito' => ['nullable', 'string', 'max:100'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'celular' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'web' => ['nullable', 'url', 'max:255'],
            'representante_legal' => ['nullable', 'string', 'max:255'],
            'tipo_empresa' => ['nullable', 'string', 'max:50'],
            'descripcion' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'activo' => ['nullable', 'boolean'],
            'es_principal' => ['nullable', 'boolean'],
        ]);

        Log::info('Actualizando empresa', ['empresa_id' => $empresa->id]);

        try {
            DB::transaction(function () use ($validated, $request, $empresa) {
                // Manejar subida de nuevo logo
                if ($request->hasFile('logo')) {
                    // Eliminar logo anterior si existe
                    if ($empresa->logo && Storage::disk('public')->exists($empresa->logo)) {
                        Storage::disk('public')->delete($empresa->logo);
                    }

                    $logoPath = $request->file('logo')->store('empresas/logos', 'public');
                    $validated['logo'] = $logoPath;
                } else {
                    // Mantener el logo existente si no se sube uno nuevo
                    unset($validated['logo']);
                }

                // Si se marca como principal, desmarcar las demás
                if ($request->has('es_principal') && $request->es_principal) {
                    Empresa::where('es_principal', true)
                        ->where('id', '!=', $empresa->id)
                        ->update(['es_principal' => false]);
                }

                $empresa->update($validated);

                Log::info('Empresa actualizada exitosamente', ['empresa_id' => $empresa->id]);
            });

            return redirect()->route('admin.empresas.index')
                ->with('success', 'Empresa actualizada exitosamente');

        } catch (ValidationException $e) {
            Log::warning('Validation failed during empresa update', ['empresa_id' => $empresa->id, 'errors' => $e->errors()]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error al actualizar empresa', [
                'empresa_id' => $empresa->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Error al actualizar empresa. Intente nuevamente.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Empresa $empresa)
    {
        Log::info('Eliminando empresa', ['empresa_id' => $empresa->id]);

        try {
            DB::transaction(function () use ($empresa) {
                // Eliminar logo si existe
                if ($empresa->logo && Storage::disk('public')->exists($empresa->logo)) {
                    Storage::disk('public')->delete($empresa->logo);
                }

                $empresa->delete();

                Log::info('Empresa eliminada exitosamente', ['empresa_id' => $empresa->id]);
            });

            return redirect()->route('admin.empresas.index')
                ->with('success', 'Empresa eliminada exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al eliminar empresa', [
                'empresa_id' => $empresa->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withErrors(['error' => 'Error al eliminar empresa. Intente nuevamente.']);
        }
    }
}
