<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:admin.productos.index')->only('index');
        $this->middleware('can:admin.productos.create')->only('create', 'store');
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
}
