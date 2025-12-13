<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Cotizacion;
use App\Models\GastoVenta;
use App\Models\HistorialEstadoEntregaVenta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:admin.ventas.index')->only('index');
        $this->middleware('can:admin.ventas.create')->only('create', 'store');
        $this->middleware('can:admin.ventas.show')->only('show');
        $this->middleware('can:admin.ventas.edit')->only('edit', 'update');
        $this->middleware('can:admin.ventas.destroy')->only('destroy');
        $this->middleware('can:admin.ventas.actualizar-estado-pedido')->only('actualizarEstadoPedido');
        $this->middleware('can:admin.ventas.update')->only('actualizarEstadoEntrega');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Venta::with(['cotizacion.cliente', 'gastos']);

        // Filtro por estado de pedido
        if ($request->filled('estado_pedido')) {
            $query->where('estado_pedido', $request->estado_pedido);
        }

        // Filtro por estado de cotización
        if ($request->filled('estado_cotizacion')) {
            $query->whereHas('cotizacion', function($q) use ($request) {
                $q->where('estado', $request->estado_cotizacion);
            });
        }

        // Si es cliente, solo ver sus propias ventas
        if (auth()->user()->hasRole('Cliente')) {
            $clienteId = auth()->user()->cliente->id ?? null;
            if ($clienteId) {
                $query->whereHas('cotizacion', function($q) use ($clienteId) {
                    $q->where('cliente_id', $clienteId);
                });
            } else {
                $query->whereRaw('1 = 0'); // No mostrar nada si no tiene cliente asociado
            }
        }

        $ventas = $query->latest()->get();

        return view('admin.ventas.index', compact('ventas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $cotizacionId = $request->get('cotizacion_id');
        $cotizacion = null;

        if ($cotizacionId) {
            $cotizacion = Cotizacion::with(['cliente', 'productos.producto'])->findOrFail($cotizacionId);

            // Validar que la cotización no tenga ya una venta
            if ($cotizacion->venta) {
                return redirect()->route('admin.ventas.show', $cotizacion->venta)
                    ->with('error', 'Esta cotización ya tiene una venta asociada.');
            }
        }

        // Obtener cotizaciones disponibles (sin venta asociada)
        $cotizacionesDisponibles = Cotizacion::with('cliente')
            ->whereDoesntHave('venta')
            ->whereIn('estado', ['pendiente', 'aprobada'])
            ->latest()
            ->get();

        return view('admin.ventas.create', compact('cotizacion', 'cotizacionesDisponibles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cotizacion_id' => ['required', 'exists:cotizaciones,id'],
            'monto_vendido' => ['required', 'numeric', 'min:0'],
            'nota' => ['nullable', 'string'],
            'estado_pedido' => ['nullable', 'in:pendiente,en_proceso,entregado,cancelado'],
            'estado_entrega' => ['nullable', 'in:' . Venta::getEstadosEntregaString()],
            'adelanto' => ['nullable', 'numeric', 'min:0'],
            'monto_transporte' => ['nullable', 'numeric', 'min:0'], // Mantener para compatibilidad
            'nombre_transporte' => ['nullable', 'string', 'max:255'], // Mantener para compatibilidad
            'codigo_seguimiento' => ['nullable', 'string', 'max:100', 'unique:ventas,codigo_seguimiento'],
            'estado_venta' => ['required', 'in:ganado,perdido'], // Estado de la cotización
            'direccion_entrega' => ['nullable', 'string'],
            'distrito' => ['nullable', 'string', 'max:100'],
            'provincia' => ['nullable', 'string', 'max:100'],
            'ciudad' => ['nullable', 'string', 'max:100'],
            'referencia' => ['nullable', 'string'],
            'codigo_postal' => ['nullable', 'string', 'max:20'],
            'gastos' => ['nullable', 'array'],
            'gastos.*.descripcion' => ['required_with:gastos', 'string', 'max:255'],
            'gastos.*.monto' => ['required_with:gastos', 'numeric', 'min:0'],
            'gastos.*.fecha' => ['nullable', 'date'],
            'gastos.*.observaciones' => ['nullable', 'string'],
        ]);

        try {
            $venta = DB::transaction(function () use ($validated) {
                $cotizacion = Cotizacion::findOrFail($validated['cotizacion_id']);

                // Validar que la cotización no tenga ya una venta
                if ($cotizacion->venta) {
                    throw new \Exception('Esta cotización ya tiene una venta asociada');
                }

                // Actualizar estado de la cotización
                $cotizacion->update([
                    'estado' => $validated['estado_venta']
                ]);

                // Generar código de seguimiento automático si no se proporciona
                $codigoSeguimiento = $validated['codigo_seguimiento'] ?? Venta::generarCodigoSeguimiento();

                // Crear la venta
                $venta = Venta::create([
                    'cotizacion_id' => $cotizacion->id,
                    'monto_vendido' => $validated['monto_vendido'],
                    'nota' => $validated['nota'] ?? null,
                    'estado_pedido' => $validated['estado_pedido'] ?? 'pendiente',
                    'estado_entrega' => $validated['estado_entrega'] ?? Venta::getEstadoEntregaDefault(),
                    'adelanto' => $validated['adelanto'] ?? 0,
                    'monto_transporte' => $validated['monto_transporte'] ?? 0, // Mantener para compatibilidad
                    'nombre_transporte' => $validated['nombre_transporte'] ?? null, // Mantener para compatibilidad
                    'codigo_seguimiento' => $codigoSeguimiento,
                    'direccion_entrega' => $validated['direccion_entrega'] ?? null,
                    'distrito' => $validated['distrito'] ?? null,
                    'provincia' => $validated['provincia'] ?? null,
                    'ciudad' => $validated['ciudad'] ?? null,
                    'referencia' => $validated['referencia'] ?? null,
                    'codigo_postal' => $validated['codigo_postal'] ?? null,
                ]);

                // Crear gastos si existen
                if (isset($validated['gastos']) && is_array($validated['gastos'])) {
                    foreach ($validated['gastos'] as $gastoData) {
                        if (!empty($gastoData['descripcion']) && isset($gastoData['monto'])) {
                            GastoVenta::create([
                                'venta_id' => $venta->id,
                                'descripcion' => $gastoData['descripcion'],
                                'monto' => $gastoData['monto'],
                                'fecha' => $gastoData['fecha'] ?? now(),
                                'observaciones' => $gastoData['observaciones'] ?? null,
                            ]);
                        }
                    }
                }

                // Registrar estado inicial en el historial
                HistorialEstadoEntregaVenta::create([
                    'venta_id' => $venta->id,
                    'estado_entrega' => $venta->estado_entrega,
                    'usuario_id' => auth()->id(),
                ]);

                // Recalcular márgenes (bruto y neto)
                $venta->refresh(); // Recargar para tener los gastos
                $venta->calcularMargenes();

                Log::info('Venta creada desde cotización', [
                    'cotizacion_id' => $cotizacion->id,
                    'venta_id' => $venta->id
                ]);

                return $venta;
            });

            return redirect()->route('admin.ventas.show', $venta)
                ->with('success', 'Venta creada exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al crear venta', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return back()->withInput()
                ->with('error', 'Error al crear la venta: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Venta $venta)
    {
        $venta->load(['cotizacion.cliente', 'cotizacion.productos.producto', 'gastos', 'historialEstadosEntrega.usuario']);

        // Si es una petición AJAX, devolver JSON
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'venta' => [
                    'id' => $venta->id,
                    'monto_vendido' => number_format($venta->monto_vendido, 2),
                    'adelanto' => number_format($venta->adelanto, 2),
                    'restante' => number_format($venta->restante, 2),
                    'estado_pedido' => $venta->estado_pedido,
                    'estado_pedido_texto' => ucfirst(str_replace('_', ' ', $venta->estado_pedido)),
                    'estado_entrega' => $venta->estado_entrega ?? Venta::getEstadoEntregaDefault(),
                    'estado_entrega_texto' => $venta->estado_entrega_texto ?? 'Registro Creado',
                    'estado_entrega_badge_class' => $venta->estado_entrega_badge_class ?? 'secondary',
                    'monto_transporte' => number_format($venta->monto_transporte, 2),
                    'nombre_transporte' => $venta->nombre_transporte ?? 'N/A',
                    'total_gastos' => number_format($venta->total_gastos, 2),
                    'margen_bruto' => number_format($venta->margen_bruto_con_transporte ?? 0, 2),
                    'margen_neto' => number_format($venta->margen_neto ?? 0, 2),
                    'codigo_seguimiento' => $venta->codigo_seguimiento ?? 'N/A',
                    'nota' => $venta->nota,
                    'fecha_creacion' => $venta->created_at->format('d/m/Y H:i'),
                    'estados_entrega_timeline' => Venta::getEstadosEntregaParaTimeline(),
                    'historial_estados_entrega' => $venta->historialEstadosEntrega->map(function($historial) {
                        return [
                            'id' => $historial->id,
                            'estado_entrega' => $historial->estado_entrega,
                            'estado_entrega_texto' => $historial->estado_entrega_texto,
                            'fecha' => $historial->created_at->format('d/m/Y'),
                            'hora' => $historial->created_at->format('H:i'),
                            'fecha_completa' => $historial->created_at->format('d/m/Y H:i'),
                            'observaciones' => $historial->observaciones,
                        ];
                    }),
                    'gastos' => $venta->gastos->map(function($gasto) {
                        return [
                            'id' => $gasto->id,
                            'descripcion' => $gasto->descripcion,
                            'monto' => number_format($gasto->monto, 2),
                            'fecha' => $gasto->fecha ? $gasto->fecha->format('d/m/Y') : null,
                            'observaciones' => $gasto->observaciones,
                        ];
                    }),
                    'cotizacion' => [
                        'numero' => $venta->cotizacion->numero_cotizacion,
                        'total' => number_format($venta->cotizacion->total, 2),
                    ]
                ]
            ]);
        }

        return view('admin.ventas.show', compact('venta'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Venta $venta)
    {
        $venta->load(['cotizacion.cliente', 'cotizacion.productos.producto', 'gastos']);

        return view('admin.ventas.edit', compact('venta'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Venta $venta)
    {
        $validated = $request->validate([
            'monto_vendido' => ['required', 'numeric', 'min:0'],
            'nota' => ['nullable', 'string'],
            'estado_pedido' => ['required', 'in:pendiente,en_proceso,entregado,cancelado'],
            'estado_entrega' => ['nullable', 'in:' . Venta::getEstadosEntregaString()],
            'adelanto' => ['nullable', 'numeric', 'min:0'],
            'monto_transporte' => ['nullable', 'numeric', 'min:0'], // Mantener para compatibilidad
            'nombre_transporte' => ['nullable', 'string', 'max:255'], // Mantener para compatibilidad
            'codigo_seguimiento' => ['nullable', 'string', 'max:100', 'unique:ventas,codigo_seguimiento,' . $venta->id],
            'direccion_entrega' => ['nullable', 'string'],
            'distrito' => ['nullable', 'string', 'max:100'],
            'provincia' => ['nullable', 'string', 'max:100'],
            'ciudad' => ['nullable', 'string', 'max:100'],
            'referencia' => ['nullable', 'string'],
            'codigo_postal' => ['nullable', 'string', 'max:20'],
            'gastos' => ['nullable', 'array'],
            'gastos.*.id' => ['nullable', 'exists:gastos_venta,id'],
            'gastos.*.descripcion' => ['required_with:gastos', 'string', 'max:255'],
            'gastos.*.monto' => ['required_with:gastos', 'numeric', 'min:0'],
            'gastos.*.fecha' => ['nullable', 'date'],
            'gastos.*.observaciones' => ['nullable', 'string'],
            'gastos_eliminar' => ['nullable', 'array'],
            'gastos_eliminar.*' => ['exists:gastos_venta,id'],
        ]);

        try {
            DB::transaction(function () use ($validated, $venta) {
                $venta->update([
                    'monto_vendido' => $validated['monto_vendido'],
                    'nota' => $validated['nota'] ?? null,
                    'estado_pedido' => $validated['estado_pedido'],
                    'estado_entrega' => $validated['estado_entrega'] ?? $venta->estado_entrega ?? 'registro_creado',
                    'adelanto' => $validated['adelanto'] ?? 0,
                    'monto_transporte' => $validated['monto_transporte'] ?? 0, // Mantener para compatibilidad
                    'nombre_transporte' => $validated['nombre_transporte'] ?? null, // Mantener para compatibilidad
                    'codigo_seguimiento' => $validated['codigo_seguimiento'] ?? $venta->codigo_seguimiento, // Mantener el existente si no se proporciona
                    'direccion_entrega' => $validated['direccion_entrega'] ?? null,
                    'distrito' => $validated['distrito'] ?? null,
                    'provincia' => $validated['provincia'] ?? null,
                    'ciudad' => $validated['ciudad'] ?? null,
                    'referencia' => $validated['referencia'] ?? null,
                    'codigo_postal' => $validated['codigo_postal'] ?? null,
                ]);

                // Eliminar gastos marcados para eliminar
                if (isset($validated['gastos_eliminar']) && is_array($validated['gastos_eliminar'])) {
                    GastoVenta::whereIn('id', $validated['gastos_eliminar'])
                        ->where('venta_id', $venta->id)
                        ->delete();
                }

                // Actualizar o crear gastos
                if (isset($validated['gastos']) && is_array($validated['gastos'])) {
                    foreach ($validated['gastos'] as $gastoData) {
                        if (!empty($gastoData['descripcion']) && isset($gastoData['monto'])) {
                            if (isset($gastoData['id']) && $gastoData['id']) {
                                // Actualizar gasto existente
                                GastoVenta::where('id', $gastoData['id'])
                                    ->where('venta_id', $venta->id)
                                    ->update([
                                        'descripcion' => $gastoData['descripcion'],
                                        'monto' => $gastoData['monto'],
                                        'fecha' => $gastoData['fecha'] ?? now(),
                                        'observaciones' => $gastoData['observaciones'] ?? null,
                                    ]);
                            } else {
                                // Crear nuevo gasto
                                GastoVenta::create([
                                    'venta_id' => $venta->id,
                                    'descripcion' => $gastoData['descripcion'],
                                    'monto' => $gastoData['monto'],
                                    'fecha' => $gastoData['fecha'] ?? now(),
                                    'observaciones' => $gastoData['observaciones'] ?? null,
                                ]);
                            }
                        }
                    }
                }

                // Recalcular márgenes (bruto y neto)
                $venta->refresh(); // Recargar para tener los gastos actualizados
                $venta->calcularMargenes();

                Log::info('Venta actualizada', ['venta_id' => $venta->id]);
            });

            return redirect()->route('admin.ventas.show', $venta)
                ->with('success', 'Venta actualizada exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al actualizar venta', [
                'venta_id' => $venta->id,
                'error' => $e->getMessage()
            ]);

            return back()->withInput()
                ->with('error', 'Error al actualizar la venta: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Venta $venta)
    {
        try {
            DB::transaction(function () use ($venta) {
                // Opcional: Revertir estado de la cotización
                $cotizacion = $venta->cotizacion;
                $cotizacion->update(['estado' => 'pendiente']);

                $venta->delete();

                Log::info('Venta eliminada', ['venta_id' => $venta->id]);
            });

            return redirect()->route('admin.ventas.index')
                ->with('success', 'Venta eliminada exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al eliminar venta', [
                'venta_id' => $venta->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Error al eliminar la venta: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar solo el estado del pedido (AJAX)
     */
    public function actualizarEstadoPedido(Request $request, Venta $venta)
    {
        $validated = $request->validate([
            'estado_pedido' => ['required', 'in:pendiente,en_proceso,entregado,cancelado'],
        ]);

        try {
            $venta->update([
                'estado_pedido' => $validated['estado_pedido']
            ]);

            Log::info('Estado de pedido actualizado', [
                'venta_id' => $venta->id,
                'nuevo_estado' => $validated['estado_pedido']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Estado de pedido actualizado correctamente',
                'estado_pedido' => $validated['estado_pedido']
            ]);

        } catch (\Exception $e) {
            Log::error('Error al actualizar estado de pedido', [
                'venta_id' => $venta->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado del pedido'
            ], 500);
        }
    }

    /**
     * Actualizar estado de entrega vía AJAX
     */
    public function actualizarEstadoEntrega(Request $request, Venta $venta)
    {
        $validated = $request->validate([
            'estado_entrega' => ['required', 'in:' . Venta::getEstadosEntregaString()],
            'observaciones' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            DB::transaction(function () use ($venta, $validated) {
                // Actualizar estado de entrega
                $venta->update([
                    'estado_entrega' => $validated['estado_entrega']
                ]);

                // Registrar en historial solo si el estado cambió
                if ($venta->wasChanged('estado_entrega')) {
                    HistorialEstadoEntregaVenta::create([
                        'venta_id' => $venta->id,
                        'estado_entrega' => $validated['estado_entrega'],
                        'usuario_id' => auth()->id(),
                        'observaciones' => $validated['observaciones'] ?? null,
                    ]);
                }
            });

            // Recargar la venta para obtener los accessors actualizados
            $venta->refresh();

            Log::info('Estado de entrega actualizado', [
                'venta_id' => $venta->id,
                'nuevo_estado' => $validated['estado_entrega']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Estado de entrega actualizado correctamente',
                'estado_entrega' => $validated['estado_entrega'],
                'estado_entrega_texto' => $venta->estado_entrega_texto,
                'estado_entrega_badge_class' => $venta->estado_entrega_badge_class
            ]);

        } catch (\Exception $e) {
            Log::error('Error al actualizar estado de entrega', [
                'venta_id' => $venta->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado de entrega'
            ], 500);
        }
    }
}
