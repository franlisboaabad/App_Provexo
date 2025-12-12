<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProductoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:admin.productos.index')->only('index');
        $this->middleware('can:admin.productos.create')->only('create', 'store', 'import');
        $this->middleware('can:admin.productos.edit')->only('edit', 'update');
        $this->middleware('can:admin.productos.show')->only('show');
        $this->middleware('can:admin.productos.destroy')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Log::info('Listando productos');

        $productos = Producto::with('proveedor.user')->get();

        return view('admin.productos.index', compact('productos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $proveedores = Proveedor::with('user')->get();
        return view('admin.productos.create', compact('proveedores'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'proveedor_id' => ['required', 'exists:proveedores,id'],
            'codigo_producto' => ['required', 'string', 'max:100', 'unique:productos,codigo_producto'],
            'descripcion' => ['required', 'string', 'max:255'],
            'precio_base' => ['required', 'numeric', 'min:0'],
            'precio_venta' => ['required', 'numeric', 'min:0'],
            'impuesto' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'unidad_medida' => ['nullable', 'string', 'max:50'],
            'imagen' => ['nullable', 'string', 'max:255'],
            'categoria' => ['nullable', 'string', 'max:100'],
            'marca' => ['nullable', 'string', 'max:100'],
        ]);

        Log::info('Creando nuevo producto', ['codigo' => $validated['codigo_producto']]);

        try {
            DB::transaction(function () use ($validated, $request) {
                Producto::create([
                    'proveedor_id' => $validated['proveedor_id'],
                    'codigo_producto' => $validated['codigo_producto'],
                    'descripcion' => $validated['descripcion'],
                    'precio_base' => $validated['precio_base'],
                    'precio_venta' => $validated['precio_venta'],
                    'impuesto' => $validated['impuesto'] ?? 0,
                    'stock' => $validated['stock'] ?? 0,
                    'unidad_medida' => $validated['unidad_medida'] ?? 'unidad',
                    'imagen' => $validated['imagen'] ?? null,
                    'categoria' => $validated['categoria'] ?? null,
                    'marca' => $validated['marca'] ?? null,
                    'activo' => $request->has('activo') ? true : false,
                ]);

                Log::info('Producto creado exitosamente', ['codigo' => $validated['codigo_producto']]);
            });

            return redirect()->route('admin.productos.index')
                ->with('success', 'Producto creado exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al crear producto', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Error al crear producto. Intente nuevamente.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Producto $producto)
    {
        $producto->load('proveedor.user');
        return view('admin.productos.show', compact('producto'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Producto $producto)
    {
        $producto->load('proveedor.user');
        $proveedores = Proveedor::with('user')->get();
        return view('admin.productos.edit', compact('producto', 'proveedores'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'proveedor_id' => ['required', 'exists:proveedores,id'],
            'codigo_producto' => ['required', 'string', 'max:100', 'unique:productos,codigo_producto,' . $producto->id],
            'descripcion' => ['required', 'string', 'max:255'],
            'precio_base' => ['required', 'numeric', 'min:0'],
            'precio_venta' => ['required', 'numeric', 'min:0'],
            'impuesto' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'unidad_medida' => ['nullable', 'string', 'max:50'],
            'imagen' => ['nullable', 'string', 'max:255'],
            'categoria' => ['nullable', 'string', 'max:100'],
            'marca' => ['nullable', 'string', 'max:100'],
        ]);

        Log::info('Actualizando producto', ['producto_id' => $producto->id]);

        try {
            DB::transaction(function () use ($validated, $producto, $request) {
                $producto->update([
                    'proveedor_id' => $validated['proveedor_id'],
                    'codigo_producto' => $validated['codigo_producto'],
                    'descripcion' => $validated['descripcion'],
                    'precio_base' => $validated['precio_base'],
                    'precio_venta' => $validated['precio_venta'],
                    'impuesto' => $validated['impuesto'] ?? 0,
                    'stock' => $validated['stock'] ?? 0,
                    'unidad_medida' => $validated['unidad_medida'] ?? 'unidad',
                    'imagen' => $validated['imagen'] ?? null,
                    'categoria' => $validated['categoria'] ?? null,
                    'marca' => $validated['marca'] ?? null,
                    'activo' => $request->has('activo') ? true : false,
                ]);

                Log::info('Producto actualizado exitosamente', ['producto_id' => $producto->id]);
            });

            return redirect()->route('admin.productos.index')
                ->with('success', 'Producto actualizado exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al actualizar producto', [
                'producto_id' => $producto->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Error al actualizar producto. Intente nuevamente.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Producto $producto)
    {
        Log::info('Eliminando producto', ['producto_id' => $producto->id]);

        try {
            DB::transaction(function () use ($producto) {
                $producto->delete();

                Log::info('Producto eliminado exitosamente', ['producto_id' => $producto->id]);
            });

            return redirect()->route('admin.productos.index')
                ->with('success', 'Producto eliminado exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al eliminar producto', [
                'producto_id' => $producto->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withErrors(['error' => 'Error al eliminar producto. Intente nuevamente.']);
        }
    }

    /**
     * Import products from CSV file
     */
    public function import(Request $request)
    {
        $request->validate([
            'archivo_csv' => ['required', 'file', 'mimes:csv,txt,xlsx,xls', 'max:10240'], // 10MB max (aumentado para Excel)
        ]);

        Log::info('Iniciando importación de productos desde archivo');

        try {
            $archivo = $request->file('archivo_csv');
            $extension = strtolower($archivo->getClientOriginalExtension());
            $rutaArchivo = $archivo->getRealPath();

            $productosImportados = 0;
            $productosDuplicados = 0;
            $productosConError = 0;
            $errores = [];

            DB::transaction(function () use ($rutaArchivo, $extension, &$productosImportados, &$productosDuplicados, &$productosConError, &$errores) {
                $filas = [];
                $encabezados = [];

                // Leer archivo según su extensión
                if (in_array($extension, ['xlsx', 'xls'])) {
                    // Leer archivo Excel
                    $spreadsheet = IOFactory::load($rutaArchivo);
                    $worksheet = $spreadsheet->getActiveSheet();
                    $highestRow = $worksheet->getHighestRow();
                    $highestColumn = $worksheet->getHighestColumn();

                    // Leer encabezados (primera fila)
                    $encabezados = [];
                    for ($col = 'A'; $col <= $highestColumn; $col++) {
                        $cell = $worksheet->getCell($col . '1');
                        $cellValue = $cell->getCalculatedValue();
                        if ($cellValue !== null && $cellValue !== '') {
                            $encabezados[] = trim(strtolower((string)$cellValue));
                        }
                    }

                    // Leer datos (desde la fila 2)
                    for ($row = 2; $row <= $highestRow; $row++) {
                        $fila = [];
                        $colIndex = 0;
                        for ($col = 'A'; $col <= $highestColumn; $col++) {
                            $cell = $worksheet->getCell($col . $row);
                            $cellValue = $cell->getCalculatedValue();

                            // Convertir el valor a string, manejando números correctamente
                            if ($cellValue !== null && $cellValue !== '') {
                                // Si es numérico, mantenerlo como string pero sin espacios
                                if (is_numeric($cellValue)) {
                                    $fila[] = (string)$cellValue;
                                } else {
                                    $fila[] = trim((string)$cellValue);
                                }
                            } else {
                                $fila[] = '';
                            }
                            $colIndex++;
                        }
                        // Solo agregar si la fila no está completamente vacía
                        if (!empty(array_filter($fila, function($val) { return $val !== ''; }))) {
                            $filas[] = $fila;
                        }
                    }
                } else {
                    // Leer archivo CSV
                    if (($handle = fopen($rutaArchivo, 'r')) !== false) {
                        // Leer encabezados
                        $encabezadosRaw = fgetcsv($handle, 1000, ',');
                        $encabezados = array_map(function($item) {
                            return trim(strtolower($item));
                        }, $encabezadosRaw);

                        // Leer datos
                        while (($fila = fgetcsv($handle, 1000, ',')) !== false) {
                            if (!empty(array_filter($fila))) {
                                $filas[] = array_map('trim', $fila);
                            }
                        }
                        fclose($handle);
                    } else {
                        throw new \Exception('No se pudo abrir el archivo');
                    }
                }

                // Validar que los encabezados sean correctos
                $encabezadosEsperados = ['codigo_producto', 'descripcion', 'precio_base', 'precio_venta', 'impuesto', 'unidad_medida'];
                $encabezadosObligatorios = ['codigo_producto', 'descripcion', 'precio_base', 'precio_venta', 'impuesto'];

                // Verificar encabezados obligatorios
                $encabezadosFaltantes = array_diff($encabezadosObligatorios, $encabezados);
                if (count($encabezadosFaltantes) > 0) {
                    throw new \Exception('Los encabezados obligatorios del archivo no son correctos. Debe contener: ' . implode(', ', $encabezadosObligatorios));
                }

                // Crear un mapa de índices
                $indices = [];
                foreach ($encabezadosEsperados as $esperado) {
                    $indice = array_search(strtolower(trim($esperado)), $encabezados);
                    if ($indice === false) {
                        // Si es unidad_medida, es opcional, continuar sin agregar al índice
                        if ($esperado === 'unidad_medida') {
                            continue;
                        }
                        throw new \Exception("Columna '{$esperado}' no encontrada en el archivo");
                    }
                    $indices[$esperado] = $indice;
                }

                // Procesar cada fila
                $numeroFila = 1; // Para reportar errores (empezamos en 1 porque la fila 1 son encabezados)
                foreach ($filas as $fila) {
                    $numeroFila++;

                    // Extraer datos según los índices
                    $codigoProducto = trim((string)($fila[$indices['codigo_producto']] ?? ''));
                    $descripcion = trim((string)($fila[$indices['descripcion']] ?? ''));
                    $precioBase = trim((string)($fila[$indices['precio_base']] ?? '0'));
                    $precioVenta = trim((string)($fila[$indices['precio_venta']] ?? '0'));
                    $impuesto = trim((string)($fila[$indices['impuesto']] ?? '0'));
                    $unidadMedida = isset($indices['unidad_medida']) ? trim((string)($fila[$indices['unidad_medida']] ?? 'unidad')) : 'unidad';

                    // Limpiar valores numéricos (remover espacios y caracteres no numéricos excepto punto decimal)
                    $precioBase = preg_replace('/[^\d.]/', '', $precioBase);
                    $precioVenta = preg_replace('/[^\d.]/', '', $precioVenta);
                    $impuesto = preg_replace('/[^\d.]/', '', $impuesto);

                    // Asegurar que los valores vacíos sean '0' para numéricos
                    $precioBase = $precioBase === '' ? '0' : $precioBase;
                    $precioVenta = $precioVenta === '' ? '0' : $precioVenta;
                    $impuesto = $impuesto === '' ? '0' : $impuesto;

                    // Validar unidad de medida (si está vacía, usar 'unidad' por defecto)
                    $unidadMedida = $unidadMedida === '' ? 'unidad' : $unidadMedida;

                    // Validar datos
                    $validator = Validator::make([
                        'codigo_producto' => $codigoProducto,
                        'descripcion' => $descripcion,
                        'precio_base' => $precioBase,
                        'precio_venta' => $precioVenta,
                        'impuesto' => $impuesto,
                        'unidad_medida' => $unidadMedida,
                    ], [
                        'codigo_producto' => ['required', 'string', 'max:100'],
                        'descripcion' => ['required', 'string', 'max:255'],
                        'precio_base' => ['required', 'numeric', 'min:0'],
                        'precio_venta' => ['required', 'numeric', 'min:0'],
                        'impuesto' => ['nullable', 'numeric', 'min:0', 'max:100'],
                        'unidad_medida' => ['nullable', 'string', 'max:50'],
                    ]);

                    if ($validator->fails()) {
                        $productosConError++;
                        $errores[] = "Fila {$numeroFila}: " . implode(', ', $validator->errors()->all());
                        continue;
                    }

                    // Verificar si el código ya existe
                    if (Producto::where('codigo_producto', $codigoProducto)->exists()) {
                        $productosDuplicados++;
                        $errores[] = "Fila {$numeroFila}: El código '{$codigoProducto}' ya existe";
                        continue;
                    }

                    // Crear producto
                    Producto::create([
                        'proveedor_id' => null,
                        'codigo_producto' => $codigoProducto,
                        'descripcion' => $descripcion,
                        'precio_base' => (float)$precioBase,
                        'precio_venta' => (float)$precioVenta,
                        'impuesto' => (float)$impuesto,
                        'stock' => 0,
                        'unidad_medida' => $unidadMedida,
                        'activo' => true,
                    ]);

                    $productosImportados++;
                }
            });

            Log::info('Importación completada', [
                'importados' => $productosImportados,
                'duplicados' => $productosDuplicados,
                'errores' => $productosConError,
            ]);

            $mensaje = "Importación completada. Productos importados: {$productosImportados}";
            if ($productosDuplicados > 0) {
                $mensaje .= ", Duplicados (omitidos): {$productosDuplicados}";
            }
            if ($productosConError > 0) {
                $mensaje .= ", Errores: {$productosConError}";
            }

            return redirect()->route('admin.productos.index')
                ->with('success', $mensaje)
                ->with('errores', $errores);

        } catch (\Exception $e) {
            Log::error('Error al importar productos', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withErrors(['error' => 'Error al importar productos: ' . $e->getMessage()]);
        }
    }
}
