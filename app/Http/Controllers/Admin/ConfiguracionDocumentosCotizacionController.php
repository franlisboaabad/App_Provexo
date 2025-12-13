<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConfiguracionDocumentosCotizacion;
use Illuminate\Http\Request;

class ConfiguracionDocumentosCotizacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:admin.configuracion-documentos.edit')->only(['edit', 'update']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        $configuracion = ConfiguracionDocumentosCotizacion::obtenerConfiguracion();
        return view('admin.configuracion-documentos-cotizacion.edit', compact('configuracion'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'observaciones' => ['nullable', 'string'],
            'condiciones_pago' => ['nullable', 'string'],
        ]);

        $configuracion = ConfiguracionDocumentosCotizacion::obtenerConfiguracion();

        $configuracion->update([
            'observaciones' => $validated['observaciones'] ?? null,
            'condiciones_pago' => $validated['condiciones_pago'] ?? null,
        ]);

        return redirect()->route('admin.configuracion-documentos.edit')
            ->with('success', 'Configuración de documentos actualizada exitosamente');
    }
}
