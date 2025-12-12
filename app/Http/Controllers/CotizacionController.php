<?php

namespace App\Http\Controllers;

use App\Models\Cotizacion;
use App\Models\CotizacionProducto;
use App\Models\Cliente;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CotizacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:admin.cotizaciones.index')->only('index');
        $this->middleware('can:admin.cotizaciones.create')->only('create', 'store');
        $this->middleware('can:admin.cotizaciones.edit')->only('edit', 'update');
        $this->middleware('can:admin.cotizaciones.show')->only('show');
        $this->middleware('can:admin.cotizaciones.destroy')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Log::info('Listando cotizaciones');

        $cotizaciones = Cotizacion::with('cliente.user', 'productos.producto')->latest()->get();

        return view('admin.cotizaciones.index', compact('cotizaciones'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clientes = Cliente::with('user')->get();
        $productos = Producto::activos()->with('proveedor')->get();

        return view('admin.cotizaciones.create', compact('clientes', 'productos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => ['required', 'exists:clientes,id'],
            'fecha_emision' => ['required', 'date'],
            'fecha_vencimiento' => ['nullable', 'date', 'after_or_equal:fecha_emision'],
            'estado' => ['required', 'in:pendiente,aprobada,rechazada,vencida'],
            'descuento' => ['nullable', 'numeric', 'min:0'],
            'observaciones' => ['nullable', 'string'],
            'productos' => ['required', 'array', 'min:1'],
            'productos.*.producto_id' => ['required', 'exists:productos,id'],
            'productos.*.cantidad' => ['required', 'integer', 'min:1'],
            'productos.*.precio_unitario' => ['required', 'numeric', 'min:0'],
            'productos.*.descuento' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        Log::info('Creando nueva cotización', ['cliente_id' => $validated['cliente_id']]);

        try {
            DB::transaction(function () use ($validated) {
                // Generar número de cotización
                $numeroCotizacion = Cotizacion::generarNumeroCotizacion();

                // Calcular totales
                $subtotal = 0;
                $impuestoTotal = 0;

                foreach ($validated['productos'] as $productoData) {
                    $producto = Producto::find($productoData['producto_id']);
                    $precioConDescuento = $productoData['precio_unitario'] - ($productoData['precio_unitario'] * ($productoData['descuento'] ?? 0) / 100);
                    $subtotalSinImpuesto = $precioConDescuento * $productoData['cantidad'];
                    $impuesto = $producto->impuesto ?? 0;
                    $impuestoMonto = $subtotalSinImpuesto * ($impuesto / 100);

                    $subtotal += $subtotalSinImpuesto;
                    $impuestoTotal += $impuestoMonto;
                }

                $descuentoGlobal = $validated['descuento'] ?? 0;
                $total = ($subtotal + $impuestoTotal) - $descuentoGlobal;

                // Crear cotización
                $cotizacion = Cotizacion::create([
                    'cliente_id' => $validated['cliente_id'],
                    'numero_cotizacion' => $numeroCotizacion,
                    'fecha_emision' => $validated['fecha_emision'],
                    'fecha_vencimiento' => $validated['fecha_vencimiento'] ?? null,
                    'estado' => $validated['estado'],
                    'subtotal' => $subtotal,
                    'descuento' => $descuentoGlobal,
                    'impuesto_total' => $impuestoTotal,
                    'total' => $total,
                    'observaciones' => $validated['observaciones'] ?? null,
                ]);

                // Crear productos de la cotización
                foreach ($validated['productos'] as $productoData) {
                    $producto = Producto::find($productoData['producto_id']);
                    $precioConDescuento = $productoData['precio_unitario'] - ($productoData['precio_unitario'] * ($productoData['descuento'] ?? 0) / 100);
                    $subtotalSinImpuesto = $precioConDescuento * $productoData['cantidad'];
                    $impuesto = $producto->impuesto ?? 0;
                    $impuestoMonto = $subtotalSinImpuesto * ($impuesto / 100);
                    $subtotalProducto = $subtotalSinImpuesto + $impuestoMonto;

                    CotizacionProducto::create([
                        'cotizacion_id' => $cotizacion->id,
                        'producto_id' => $productoData['producto_id'],
                        'cantidad' => $productoData['cantidad'],
                        'precio_unitario' => $productoData['precio_unitario'],
                        'descuento' => $productoData['descuento'] ?? 0,
                        'impuesto' => $impuesto,
                        'subtotal' => $subtotalProducto,
                    ]);
                }

                Log::info('Cotización creada exitosamente', [
                    'cotizacion_id' => $cotizacion->id,
                    'numero_cotizacion' => $numeroCotizacion
                ]);
            });

            return redirect()->route('admin.cotizaciones.index')
                ->with('success', 'Cotización creada exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al crear cotización', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Error al crear cotización. Intente nuevamente.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Cotizacion $cotizacione)
    {
        $cotizacione->load('cliente.user', 'productos.producto');
        return view('admin.cotizaciones.show', compact('cotizacione'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cotizacion $cotizacione)
    {
        Log::info('Editando cotización', ['cotizacion_id' => $cotizacione->id]);
        $cotizacione->load('cliente.user', 'productos.producto');
        $clientes = Cliente::with('user')->get();
        $productos = Producto::activos()->with('proveedor')->get();

        return view('admin.cotizaciones.edit', compact('cotizacione', 'clientes', 'productos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cotizacion $cotizacione)
    {
        $validated = $request->validate([
            'cliente_id' => ['required', 'exists:clientes,id'],
            'fecha_emision' => ['required', 'date'],
            'fecha_vencimiento' => ['nullable', 'date', 'after_or_equal:fecha_emision'],
            'estado' => ['required', 'in:pendiente,aprobada,rechazada,vencida'],
            'descuento' => ['nullable', 'numeric', 'min:0'],
            'observaciones' => ['nullable', 'string'],
            'productos' => ['required', 'array', 'min:1'],
            'productos.*.producto_id' => ['required', 'exists:productos,id'],
            'productos.*.cantidad' => ['required', 'integer', 'min:1'],
            'productos.*.precio_unitario' => ['required', 'numeric', 'min:0'],
            'productos.*.descuento' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        Log::info('Actualizando cotización', ['cotizacion_id' => $cotizacione->id]);

        try {
            DB::transaction(function () use ($validated, $cotizacione) {
                // Eliminar productos anteriores
                $cotizacione->productos()->delete();

                // Calcular totales
                $subtotal = 0;
                $impuestoTotal = 0;

                foreach ($validated['productos'] as $productoData) {
                    $producto = Producto::find($productoData['producto_id']);
                    $precioConDescuento = $productoData['precio_unitario'] - ($productoData['precio_unitario'] * ($productoData['descuento'] ?? 0) / 100);
                    $subtotalSinImpuesto = $precioConDescuento * $productoData['cantidad'];
                    $impuesto = $producto->impuesto ?? 0;
                    $impuestoMonto = $subtotalSinImpuesto * ($impuesto / 100);

                    $subtotal += $subtotalSinImpuesto;
                    $impuestoTotal += $impuestoMonto;
                }

                $descuentoGlobal = $validated['descuento'] ?? 0;
                $total = ($subtotal + $impuestoTotal) - $descuentoGlobal;

                // Actualizar cotización
                $cotizacione->update([
                    'cliente_id' => $validated['cliente_id'],
                    'fecha_emision' => $validated['fecha_emision'],
                    'fecha_vencimiento' => $validated['fecha_vencimiento'] ?? null,
                    'estado' => $validated['estado'],
                    'subtotal' => $subtotal,
                    'descuento' => $descuentoGlobal,
                    'impuesto_total' => $impuestoTotal,
                    'total' => $total,
                    'observaciones' => $validated['observaciones'] ?? null,
                ]);

                // Crear productos actualizados
                foreach ($validated['productos'] as $productoData) {
                    $producto = Producto::find($productoData['producto_id']);
                    $precioConDescuento = $productoData['precio_unitario'] - ($productoData['precio_unitario'] * ($productoData['descuento'] ?? 0) / 100);
                    $subtotalSinImpuesto = $precioConDescuento * $productoData['cantidad'];
                    $impuesto = $producto->impuesto ?? 0;
                    $impuestoMonto = $subtotalSinImpuesto * ($impuesto / 100);
                    $subtotalProducto = $subtotalSinImpuesto + $impuestoMonto;

                    CotizacionProducto::create([
                        'cotizacion_id' => $cotizacione->id,
                        'producto_id' => $productoData['producto_id'],
                        'cantidad' => $productoData['cantidad'],
                        'precio_unitario' => $productoData['precio_unitario'],
                        'descuento' => $productoData['descuento'] ?? 0,
                        'impuesto' => $impuesto,
                        'subtotal' => $subtotalProducto,
                    ]);
                }

                Log::info('Cotización actualizada exitosamente', ['cotizacion_id' => $cotizacione->id]);
            });

            return redirect()->route('admin.cotizaciones.index')
                ->with('success', 'Cotización actualizada exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al actualizar cotización', [
                'cotizacion_id' => $cotizacione->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Error al actualizar cotización. Intente nuevamente.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cotizacion $cotizacione)
    {
        Log::info('Eliminando cotización', ['cotizacion_id' => $cotizacione->id]);

        try {
            DB::transaction(function () use ($cotizacione) {
                $cotizacione->productos()->delete();
                $cotizacione->delete();

                Log::info('Cotización eliminada exitosamente', ['cotizacion_id' => $cotizacione->id]);
            });

            return redirect()->route('admin.cotizaciones.index')
                ->with('success', 'Cotización eliminada exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al eliminar cotización', [
                'cotizacion_id' => $cotizacione->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withErrors(['error' => 'Error al eliminar cotización. Intente nuevamente.']);
        }
    }
}
