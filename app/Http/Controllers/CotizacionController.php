<?php

namespace App\Http\Controllers;

use App\Models\Cotizacion;
use App\Models\CotizacionProducto;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Empresa;
use App\Models\HistorialPrecioProducto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail as MailFacade;
use Illuminate\Support\Facades\Auth;
use Dompdf\Dompdf;
use Dompdf\Options;

class CotizacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Los clientes pueden ver sus propias cotizaciones, pero no crear/editar
        $this->middleware('can:admin.cotizaciones.create')->only('create', 'store');
        $this->middleware('can:admin.cotizaciones.edit')->only('edit', 'update', 'cambiarEstado');
        $this->middleware('can:admin.cotizaciones.publica')->only('publica');
        $this->middleware('can:admin.cotizaciones.enviar-email')->only('enviarEmail');
        $this->middleware('can:admin.cotizaciones.venta')->only('obtenerVenta');
        $this->middleware('can:admin.cotizaciones.destroy')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        Log::info('Listando cotizaciones', ['user_id' => $user->id, 'role' => $user->roles->pluck('name')->toArray()]);

        // Si el usuario es Cliente, mostrar solo sus cotizaciones
        if ($user->hasRole('Cliente')) {
            $cliente = $user->cliente;

            if (!$cliente) {
                return redirect()->route('profile.edit')
                    ->with('warning', 'Por favor completa tu perfil de cliente.');
            }

            $query = Cotizacion::where('cliente_id', $cliente->id)
                ->with('cliente.user', 'productos.producto', 'venta');

            // Filtrar por estado si se proporciona
            if ($request->has('estado') && $request->estado) {
                $query->where('estado', $request->estado);
            }

            $cotizaciones = $query->latest()->get();
        } else {
            // Para administradores, mostrar todas las cotizaciones
            if (!$user->can('admin.cotizaciones.index')) {
                abort(403, 'No tienes permiso para ver cotizaciones.');
            }

            $query = Cotizacion::with('cliente.user', 'productos.producto', 'venta');

            // Filtrar por estado si se proporciona
            if ($request->has('estado') && $request->estado) {
                $query->where('estado', $request->estado);
            }

            $cotizaciones = $query->latest()->get();
        }

        return view('admin.cotizaciones.index', compact('cotizaciones'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clientes = Cliente::with('user')->get();
        $productos = Producto::activos()->with('proveedor')->get();

        // Si hay una cotización creada, cargarla para el modal
        $cotizacion = null;
        if (session('cotizacion_creada')) {
            $cotizacion = Cotizacion::with('cliente.user', 'productos.producto')->find(session('cotizacion_creada'));
        }

        return view('admin.cotizaciones.create', compact('clientes', 'productos', 'cotizacion'));
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
            'productos.*.peso_unidad' => ['nullable', 'numeric', 'min:0'],
            'productos.*.flete_tonelada' => ['nullable', 'numeric', 'min:0'],
            'productos.*.margen_porcentaje' => ['nullable', 'numeric', 'min:0'],
            'productos.*.flete_unitario' => ['nullable', 'numeric', 'min:0'],
            'productos.*.costo_mas_flete' => ['nullable', 'numeric', 'min:0'],
            'productos.*.total_kg' => ['nullable', 'numeric', 'min:0'],
            'productos.*.margen_total' => ['nullable', 'numeric', 'min:0'],
            'productos.*.flete_total' => ['nullable', 'numeric', 'min:0'],
            'productos.*.precio_base_original' => ['nullable', 'numeric', 'min:0'],
            'productos.*.precio_base_cotizacion' => ['nullable', 'numeric', 'min:0'],
            'actualizar_precio_base' => ['nullable', 'boolean'],
        ]);

        Log::info('Creando nueva cotización', ['cliente_id' => $validated['cliente_id']]);

        try {
            $cotizacionCreada = null;
            $numeroCotizacion = null;

            DB::transaction(function () use ($validated, &$cotizacionCreada, &$numeroCotizacion) {
                // Generar número de cotización
                $numeroCotizacion = Cotizacion::generarNumeroCotizacion();

                // Generar token público único
                do {
                    $tokenPublico = \Illuminate\Support\Str::random(64);
                } while (Cotizacion::where('token_publico', $tokenPublico)->exists());

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
                    'token_publico' => $tokenPublico,
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

                    $precioBaseCotizacion = $productoData['precio_base_cotizacion'] ?? $producto->precio_base;
                    $precioBaseOriginal = $productoData['precio_base_original'] ?? $producto->precio_base;

                    // Verificar si el precio base cambió y registrar en historial
                    if (isset($productoData['precio_base_cotizacion']) &&
                        abs($precioBaseOriginal - $precioBaseCotizacion) > 0.01) {
                        HistorialPrecioProducto::create([
                            'producto_id' => $producto->id,
                            'cotizacion_id' => $cotizacion->id,
                            'usuario_id' => Auth::id(),
                            'precio_base_anterior' => $precioBaseOriginal,
                            'precio_base_nuevo' => $precioBaseCotizacion,
                            'observaciones' => "Cambio de precio base en cotización {$cotizacion->numero_cotizacion}",
                        ]);

                        // Actualizar precio base del producto si está marcado
                        if ($validated['actualizar_precio_base'] ?? false) {
                            $producto->update(['precio_base' => $precioBaseCotizacion]);
                            Log::info('Precio base del producto actualizado', [
                                'producto_id' => $producto->id,
                                'precio_anterior' => $precioBaseOriginal,
                                'precio_nuevo' => $precioBaseCotizacion,
                            ]);
                        }

                        Log::info('Precio base registrado en historial', [
                            'producto_id' => $producto->id,
                            'cotizacion_id' => $cotizacion->id,
                            'precio_anterior' => $precioBaseOriginal,
                            'precio_nuevo' => $precioBaseCotizacion,
                        ]);
                    }

                    CotizacionProducto::create([
                        'cotizacion_id' => $cotizacion->id,
                        'producto_id' => $productoData['producto_id'],
                        'cantidad' => $productoData['cantidad'],
                        'precio_unitario' => $productoData['precio_unitario'],
                        'precio_base_cotizacion' => $precioBaseCotizacion,
                        'descuento' => $productoData['descuento'] ?? 0,
                        'impuesto' => $impuesto,
                        'subtotal' => $subtotalProducto,
                        'peso_unidad' => $productoData['peso_unidad'] ?? null,
                        'flete_tonelada' => $productoData['flete_tonelada'] ?? null,
                        'margen_porcentaje' => $productoData['margen_porcentaje'] ?? null,
                        'flete_unitario' => $productoData['flete_unitario'] ?? null,
                        'costo_mas_flete' => $productoData['costo_mas_flete'] ?? null,
                        'total_kg' => $productoData['total_kg'] ?? null,
                        'margen_total' => $productoData['margen_total'] ?? null,
                        'flete_total' => $productoData['flete_total'] ?? null,
                    ]);
                }

                $cotizacionCreada = $cotizacion;

                Log::info('Cotización creada exitosamente', [
                    'cotizacion_id' => $cotizacion->id,
                    'numero_cotizacion' => $numeroCotizacion
                ]);
            });

            // Validar que se creó la cotización
            if (!$cotizacionCreada) {
                throw new \Exception('No se pudo crear la cotización');
            }

            return redirect()->route('admin.cotizaciones.create')
                ->with('cotizacion_creada', $cotizacionCreada->id)
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
    public function show(Request $request, Cotizacion $cotizacione)
    {
        $user = $request->user();

        // Si el usuario es Cliente, verificar que la cotización sea suya
        if ($user->hasRole('Cliente')) {
            $cliente = $user->cliente;

            if (!$cliente || $cotizacione->cliente_id !== $cliente->id) {
                abort(403, 'No tienes permiso para ver esta cotización.');
            }
        } else {
            // Para administradores, verificar permiso
            if (!$user->can('admin.cotizaciones.show')) {
                abort(403, 'No tienes permiso para ver cotizaciones.');
            }
        }

        $cotizacione->load('cliente.user', 'productos.producto', 'venta');
        return view('admin.cotizaciones.show', compact('cotizacione'));
    }

    /**
     * Obtener datos de venta de una cotización (para AJAX)
     */
    public function obtenerVenta(Cotizacion $cotizacione)
    {
        $cotizacione->load('venta');

        return response()->json([
            'success' => true,
            'cotizacion' => [
                'id' => $cotizacione->id,
                'estado' => $cotizacione->estado,
            ],
            'venta' => $cotizacione->venta ? [
                'id' => $cotizacione->venta->id,
                'monto_vendido' => $cotizacione->venta->monto_vendido,
                'nota' => $cotizacione->venta->nota,
                'estado_pedido' => $cotizacione->venta->estado_pedido,
                'adelanto' => $cotizacione->venta->adelanto,
                'monto_transporte' => $cotizacione->venta->monto_transporte,
                'nombre_transporte' => $cotizacione->venta->nombre_transporte,
                'margen_bruto_con_transporte' => $cotizacione->venta->margen_bruto_con_transporte,
            ] : null
        ]);
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
            'productos.*.peso_unidad' => ['nullable', 'numeric', 'min:0'],
            'productos.*.flete_tonelada' => ['nullable', 'numeric', 'min:0'],
            'productos.*.margen_porcentaje' => ['nullable', 'numeric', 'min:0'],
            'productos.*.flete_unitario' => ['nullable', 'numeric', 'min:0'],
            'productos.*.costo_mas_flete' => ['nullable', 'numeric', 'min:0'],
            'productos.*.total_kg' => ['nullable', 'numeric', 'min:0'],
            'productos.*.margen_total' => ['nullable', 'numeric', 'min:0'],
            'productos.*.flete_total' => ['nullable', 'numeric', 'min:0'],
            'productos.*.precio_base_original' => ['nullable', 'numeric', 'min:0'],
            'productos.*.precio_base_cotizacion' => ['nullable', 'numeric', 'min:0'],
            'actualizar_precio_base' => ['nullable', 'boolean'],
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

                    $precioBaseCotizacion = $productoData['precio_base_cotizacion'] ?? $producto->precio_base;
                    $precioBaseOriginal = $productoData['precio_base_original'] ?? $producto->precio_base;

                    // Verificar si el precio base cambió y registrar en historial
                    if (isset($productoData['precio_base_cotizacion']) &&
                        abs($precioBaseOriginal - $precioBaseCotizacion) > 0.01) {
                        HistorialPrecioProducto::create([
                            'producto_id' => $producto->id,
                            'cotizacion_id' => $cotizacione->id,
                            'usuario_id' => Auth::id(),
                            'precio_base_anterior' => $precioBaseOriginal,
                            'precio_base_nuevo' => $precioBaseCotizacion,
                            'observaciones' => "Cambio de precio base en cotización {$cotizacione->numero_cotizacion} (actualización)",
                        ]);

                        // Actualizar precio base del producto si está marcado
                        if ($validated['actualizar_precio_base'] ?? false) {
                            $producto->update(['precio_base' => $precioBaseCotizacion]);
                            Log::info('Precio base del producto actualizado (actualización)', [
                                'producto_id' => $producto->id,
                                'precio_anterior' => $precioBaseOriginal,
                                'precio_nuevo' => $precioBaseCotizacion,
                            ]);
                        }

                        Log::info('Precio base registrado en historial (actualización)', [
                            'producto_id' => $producto->id,
                            'cotizacion_id' => $cotizacione->id,
                            'precio_anterior' => $precioBaseOriginal,
                            'precio_nuevo' => $precioBaseCotizacion,
                        ]);
                    }

                    CotizacionProducto::create([
                        'cotizacion_id' => $cotizacione->id,
                        'producto_id' => $productoData['producto_id'],
                        'cantidad' => $productoData['cantidad'],
                        'precio_unitario' => $productoData['precio_unitario'],
                        'precio_base_cotizacion' => $precioBaseCotizacion,
                        'descuento' => $productoData['descuento'] ?? 0,
                        'impuesto' => $impuesto,
                        'subtotal' => $subtotalProducto,
                        'peso_unidad' => $productoData['peso_unidad'] ?? null,
                        'flete_tonelada' => $productoData['flete_tonelada'] ?? null,
                        'margen_porcentaje' => $productoData['margen_porcentaje'] ?? null,
                        'flete_unitario' => $productoData['flete_unitario'] ?? null,
                        'costo_mas_flete' => $productoData['costo_mas_flete'] ?? null,
                        'total_kg' => $productoData['total_kg'] ?? null,
                        'margen_total' => $productoData['margen_total'] ?? null,
                        'flete_total' => $productoData['flete_total'] ?? null,
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
     * Cambiar el estado de una cotización
     */
    public function cambiarEstado(Request $request, Cotizacion $cotizacione)
    {
        $validated = $request->validate([
            'estado' => ['required', 'in:pendiente,aprobada,rechazada,vencida,ganado,perdido'],
        ]);

        try {
            $cotizacione->update([
                'estado' => $validated['estado']
            ]);

            Log::info('Estado de cotización actualizado', [
                'cotizacion_id' => $cotizacione->id,
                'nuevo_estado' => $validated['estado']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado correctamente',
                'estado' => $validated['estado'],
                'estado_texto' => ucfirst($validated['estado'])
            ]);
        } catch (\Exception $e) {
            Log::error('Error al cambiar estado de cotización', [
                'cotizacion_id' => $cotizacione->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado'
            ], 500);
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

    /**
     * Generar PDF de la cotización
     */
    public function pdf(Request $request, Cotizacion $cotizacione)
    {
        $user = $request->user();

        // Si el usuario es Cliente, verificar que la cotización sea suya
        if ($user->hasRole('Cliente')) {
            $cliente = $user->cliente;

            if (!$cliente || $cotizacione->cliente_id !== $cliente->id) {
                abort(403, 'No tienes permiso para descargar esta cotización.');
            }
        } else {
            // Para administradores, verificar permiso
            if (!$user->can('admin.cotizaciones.show')) {
                abort(403, 'No tienes permiso para descargar cotizaciones.');
            }
        }

        Log::info('Generando PDF de cotización', ['cotizacion_id' => $cotizacione->id]);

        try {
            // Cargar relaciones necesarias
            $cotizacione->load('cliente.user', 'productos.producto');

            // Obtener empresa principal
            $empresa = Empresa::where('es_principal', true)->first();

            // Si no hay empresa principal, obtener la primera activa
            if (!$empresa) {
                $empresa = Empresa::activas()->first();
            }

            // Si todavía no hay empresa, crear una estructura vacía
            if (!$empresa) {
                $empresa = new Empresa();
            }

            // Cargar cuentas bancarias activas
            $cuentasBancarias = $empresa->id ? $empresa->cuentasBancarias()->where('activa', true)->get() : collect([]);

            // Obtener configuración de documentos
            $configuracion = \App\Models\ConfiguracionDocumentosCotizacion::obtenerConfiguracion();

            // Renderizar la vista
            $html = view('admin.cotizaciones.pdf', compact('cotizacione', 'empresa', 'cuentasBancarias', 'configuracion'))->render();

            // Configurar opciones de Dompdf
            $options = new Options();
            $options->set('isRemoteEnabled', true);
            $options->set('isHtml5ParserEnabled', true);
            $options->set('defaultFont', 'Arial');

            // Crear instancia de Dompdf
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Generar nombre del archivo
            $filename = 'cotizacion-' . $cotizacione->numero_cotizacion . '.pdf';

            // Descargar el PDF
            return $dompdf->stream($filename, ['Attachment' => false]);

        } catch (\Exception $e) {
            Log::error('Error al generar PDF de cotización', [
                'cotizacion_id' => $cotizacione->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withErrors(['error' => 'Error al generar el PDF. Intente nuevamente.']);
        }
    }

    /**
     * Obtener URL pública de la cotización
     */
    public function publica(Cotizacion $cotizacione)
    {
        if (!$cotizacione->token_publico) {
            // Generar token si no existe
            do {
                $tokenPublico = \Illuminate\Support\Str::random(64);
            } while (Cotizacion::where('token_publico', $tokenPublico)->exists());

            $cotizacione->update(['token_publico' => $tokenPublico]);
        }

        $urlPublica = route('cotizacion.publica', $cotizacione->token_publico);

        return response()->json([
            'success' => true,
            'url' => $urlPublica
        ]);
    }

    /**
     * Ver cotización pública (sin autenticación)
     */
    public function verPublica($token)
    {
        $cotizacione = Cotizacion::where('token_publico', $token)
            ->with('cliente.user', 'productos.producto')
            ->firstOrFail();

        // Obtener empresa principal
        $empresa = Empresa::where('es_principal', true)->first();
        if (!$empresa) {
            $empresa = Empresa::activas()->first();
        }
        if (!$empresa) {
            $empresa = new Empresa();
        }

        $cuentasBancarias = $empresa->id ? $empresa->cuentasBancarias()->where('activa', true)->get() : collect([]);

        // Renderizar vista pública del PDF
        $html = view('admin.cotizaciones.pdf', compact('cotizacione', 'empresa', 'cuentasBancarias'))->render();

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'cotizacion-' . $cotizacione->numero_cotizacion . '.pdf';
        return $dompdf->stream($filename, ['Attachment' => false]);
    }

    /**
     * Enviar cotización por email
     */
    public function enviarEmail(Request $request, Cotizacion $cotizacione)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        try {
            // Obtener URL pública
            if (!$cotizacione->token_publico) {
                do {
                    $tokenPublico = \Illuminate\Support\Str::random(64);
                } while (Cotizacion::where('token_publico', $tokenPublico)->exists());
                $cotizacione->update(['token_publico' => $tokenPublico]);
            }

            $urlPublica = route('cotizacion.publica', $cotizacione->token_publico);

            // Enviar email (implementación básica - puedes mejorarla con una clase Mailable)
            $mensaje = "Hola,\n\n";
            $mensaje .= "Se ha generado una nueva cotización para usted.\n\n";
            $mensaje .= "Número de Cotización: {$cotizacione->numero_cotizacion}\n";
            $mensaje .= "Total: S/ " . number_format($cotizacione->total, 2) . "\n\n";
            $mensaje .= "Puede ver y descargar la cotización en el siguiente enlace:\n";
            $mensaje .= $urlPublica . "\n\n";
            $mensaje .= "Saludos cordiales.";

            MailFacade::raw($mensaje, function ($message) use ($request, $cotizacione) {
                $message->to($request->email)
                    ->subject('Cotización #' . $cotizacione->numero_cotizacion);
            });

            return response()->json([
                'success' => true,
                'message' => 'Cotización enviada por email exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al enviar email de cotización', [
                'cotizacion_id' => $cotizacione->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el email. Intente nuevamente.'
            ], 500);
        }
    }
}
