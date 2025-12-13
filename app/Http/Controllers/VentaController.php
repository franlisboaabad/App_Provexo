<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Cotizacion;
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
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Venta::with(['cotizacion.cliente']);

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
            'adelanto' => ['nullable', 'numeric', 'min:0'],
            'monto_transporte' => ['nullable', 'numeric', 'min:0'],
            'nombre_transporte' => ['nullable', 'string', 'max:255'],
            'estado_venta' => ['required', 'in:ganado,perdido'], // Estado de la cotización
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

                // Crear la venta
                $venta = Venta::create([
                    'cotizacion_id' => $cotizacion->id,
                    'monto_vendido' => $validated['monto_vendido'],
                    'nota' => $validated['nota'] ?? null,
                    'estado_pedido' => $validated['estado_pedido'] ?? 'pendiente',
                    'adelanto' => $validated['adelanto'] ?? 0,
                    'monto_transporte' => $validated['monto_transporte'] ?? 0,
                    'nombre_transporte' => $validated['nombre_transporte'] ?? null,
                ]);

                // Calcular margen bruto con transporte
                $venta->calcularMargenBruto();

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
        $venta->load(['cotizacion.cliente', 'cotizacion.productos.producto']);

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
                    'monto_transporte' => number_format($venta->monto_transporte, 2),
                    'nombre_transporte' => $venta->nombre_transporte ?? 'N/A',
                    'nota' => $venta->nota,
                    'fecha_creacion' => $venta->created_at->format('d/m/Y H:i'),
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
        $venta->load(['cotizacion.cliente', 'cotizacion.productos.producto']);

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
            'adelanto' => ['nullable', 'numeric', 'min:0'],
            'monto_transporte' => ['nullable', 'numeric', 'min:0'],
            'nombre_transporte' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            DB::transaction(function () use ($validated, $venta) {
                $venta->update([
                    'monto_vendido' => $validated['monto_vendido'],
                    'nota' => $validated['nota'] ?? null,
                    'estado_pedido' => $validated['estado_pedido'],
                    'adelanto' => $validated['adelanto'] ?? 0,
                    'monto_transporte' => $validated['monto_transporte'] ?? 0,
                    'nombre_transporte' => $validated['nombre_transporte'] ?? null,
                ]);

                // Recalcular margen bruto
                $venta->calcularMargenBruto();

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
}
