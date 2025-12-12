<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentoCliente;
use App\Models\Cliente;
use App\Models\Cotizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DocumentoClienteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:admin.documentos-clientes.index')->only('index');
        $this->middleware('can:admin.documentos-clientes.create')->only('create', 'store');
        $this->middleware('can:admin.documentos-clientes.edit')->only('edit', 'update');
        $this->middleware('can:admin.documentos-clientes.show')->only('show');
        $this->middleware('can:admin.documentos-clientes.destroy')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Log::info('Listando documentos de clientes');

        $user = auth()->user();
        
        // Si el usuario es Cliente, mostrar solo sus documentos
        if ($user->hasRole('Cliente')) {
            $cliente = $user->cliente;
            
            if (!$cliente) {
                return redirect()->route('profile.edit')
                    ->with('warning', 'Por favor completa tu perfil de cliente.');
            }

            $query = DocumentoCliente::where('cliente_id', $cliente->id)
                ->with('cliente.user', 'cotizacion', 'usuario');

            // Filtrar por tipo de documento si se proporciona
            if ($request->has('tipo_documento') && $request->tipo_documento) {
                $query->where('tipo_documento', $request->tipo_documento);
            }

            $documentos = $query->latest()->get();
            $clientes = collect([$cliente]); // Solo su propio cliente
        } else {
            // Para administradores, mostrar todos los documentos
            $query = DocumentoCliente::with('cliente.user', 'cotizacion', 'usuario');

            // Filtrar por cliente si se proporciona
            if ($request->has('cliente_id') && $request->cliente_id) {
                $query->where('cliente_id', $request->cliente_id);
            }

            // Filtrar por cotización si se proporciona
            if ($request->has('cotizacion_id') && $request->cotizacion_id) {
                $query->where('cotizacion_id', $request->cotizacion_id);
            }

            // Filtrar por tipo de documento si se proporciona
            if ($request->has('tipo_documento') && $request->tipo_documento) {
                $query->where('tipo_documento', $request->tipo_documento);
            }

            $documentos = $query->latest()->get();
            $clientes = Cliente::with('user')->get();
        }

        return view('admin.documentos-clientes.index', compact('documentos', 'clientes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $clientes = Cliente::with('user')->get();
        $cotizaciones = collect();
        $clienteSeleccionado = null;

        // Si se proporciona un cliente_id, cargar sus cotizaciones
        if ($request->has('cliente_id') && $request->cliente_id) {
            $clienteSeleccionado = Cliente::with('user')->find($request->cliente_id);
            if ($clienteSeleccionado) {
                $cotizaciones = Cotizacion::where('cliente_id', $clienteSeleccionado->id)
                    ->orderBy('fecha_emision', 'desc')
                    ->get();
            }
        }

        return view('admin.documentos-clientes.create', compact('clientes', 'cotizaciones', 'clienteSeleccionado'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => ['required', 'exists:clientes,id'],
            'cotizacion_id' => ['nullable', 'exists:cotizaciones,id'],
            'titulo' => ['required', 'string', 'max:255'],
            'archivo' => ['required', 'file', 'max:10240'], // 10MB máximo
            'tipo_documento' => ['required', 'in:factura,contrato,garantia,orden_compra,otro'],
            'numero_documento' => ['nullable', 'string', 'max:100'],
            'fecha_documento' => ['nullable', 'date'],
            'observaciones' => ['nullable', 'string'],
        ]);

        Log::info('Creando nuevo documento de cliente', ['cliente_id' => $validated['cliente_id']]);

        try {
            DB::transaction(function () use ($validated, $request) {
                // Manejar subida de archivo
                $archivo = $request->file('archivo');
                $nombreArchivo = $archivo->getClientOriginalName();
                $rutaArchivo = $archivo->store('documentos-clientes', 'public');

                DocumentoCliente::create([
                    'cliente_id' => $validated['cliente_id'],
                    'cotizacion_id' => $validated['cotizacion_id'] ?? null,
                    'titulo' => $validated['titulo'],
                    'nombre_archivo' => $nombreArchivo,
                    'ruta_archivo' => $rutaArchivo,
                    'tipo_documento' => $validated['tipo_documento'],
                    'numero_documento' => $validated['numero_documento'] ?? null,
                    'fecha_documento' => $validated['fecha_documento'] ?? null,
                    'usuario_id' => auth()->id(),
                    'observaciones' => $validated['observaciones'] ?? null,
                    'activo' => true,
                ]);

                Log::info('Documento creado exitosamente', ['cliente_id' => $validated['cliente_id']]);
            });

            return redirect()->route('admin.documentos-clientes.index')
                ->with('success', 'Documento creado exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al crear documento', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Error al crear documento. Intente nuevamente.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $documentoCliente = DocumentoCliente::with('cliente.user', 'cotizacion', 'usuario')->findOrFail($id);
        
        // Si el usuario es Cliente, verificar que el documento le pertenezca
        if (auth()->user()->hasRole('Cliente')) {
            $cliente = auth()->user()->cliente;
            if (!$cliente || $documentoCliente->cliente_id !== $cliente->id) {
                abort(403, 'No tienes permiso para ver este documento.');
            }
        }
        
        Log::info('Mostrando documento', ['documento_id' => $documentoCliente->id]);

        return view('admin.documentos-clientes.show', compact('documentoCliente'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id)
    {
        $documentoCliente = DocumentoCliente::findOrFail($id);
        $clientes = Cliente::with('user')->get();
        $cotizaciones = collect();
        $clienteId = $request->has('cliente_id') && $request->cliente_id 
            ? $request->cliente_id 
            : $documentoCliente->cliente_id;

        if ($clienteId) {
            $cotizaciones = Cotizacion::where('cliente_id', $clienteId)
                ->orderBy('fecha_emision', 'desc')
                ->get();
        }

        return view('admin.documentos-clientes.edit', compact('documentoCliente', 'clientes', 'cotizaciones'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $documentoCliente = DocumentoCliente::findOrFail($id);
        
        $validated = $request->validate([
            'cliente_id' => ['required', 'exists:clientes,id'],
            'cotizacion_id' => ['nullable', 'exists:cotizaciones,id'],
            'titulo' => ['required', 'string', 'max:255'],
            'archivo' => ['nullable', 'file', 'max:10240'], // 10MB máximo
            'tipo_documento' => ['required', 'in:factura,contrato,garantia,orden_compra,otro'],
            'numero_documento' => ['nullable', 'string', 'max:100'],
            'fecha_documento' => ['nullable', 'date'],
            'observaciones' => ['nullable', 'string'],
            'activo' => ['nullable', 'boolean'],
        ]);

        Log::info('Actualizando documento', ['documento_id' => $documentoCliente->id]);

        try {
            DB::transaction(function () use ($validated, $request, $documentoCliente) {
                // Manejar subida de nuevo archivo
                if ($request->hasFile('archivo')) {
                    // Eliminar archivo anterior si existe
                    if ($documentoCliente->ruta_archivo && Storage::disk('public')->exists($documentoCliente->ruta_archivo)) {
                        Storage::disk('public')->delete($documentoCliente->ruta_archivo);
                    }

                    $archivo = $request->file('archivo');
                    $nombreArchivo = $archivo->getClientOriginalName();
                    $rutaArchivo = $archivo->store('documentos-clientes', 'public');

                    $validated['nombre_archivo'] = $nombreArchivo;
                    $validated['ruta_archivo'] = $rutaArchivo;
                } else {
                    // Mantener el archivo existente si no se sube uno nuevo
                    unset($validated['archivo']);
                }

                $documentoCliente->update($validated);

                Log::info('Documento actualizado exitosamente', ['documento_id' => $documentoCliente->id]);
            });

            return redirect()->route('admin.documentos-clientes.index')
                ->with('success', 'Documento actualizado exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al actualizar documento', [
                'documento_id' => $documentoCliente->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Error al actualizar documento. Intente nuevamente.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $documentoCliente = DocumentoCliente::findOrFail($id);
        
        Log::info('Eliminando documento', ['documento_id' => $documentoCliente->id]);

        try {
            DB::transaction(function () use ($documentoCliente) {
                // Eliminar archivo físico
                if ($documentoCliente->ruta_archivo && Storage::disk('public')->exists($documentoCliente->ruta_archivo)) {
                    Storage::disk('public')->delete($documentoCliente->ruta_archivo);
                }

                // Eliminar registro
                $documentoCliente->delete();

                Log::info('Documento eliminado exitosamente', ['documento_id' => $documentoCliente->id]);
            });

            return redirect()->route('admin.documentos-clientes.index')
                ->with('success', 'Documento eliminado exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al eliminar documento', [
                'documento_id' => $documentoCliente->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withErrors(['error' => 'Error al eliminar documento. Intente nuevamente.']);
        }
    }

    /**
     * Descargar el archivo del documento
     */
    public function download($id)
    {
        $documentoCliente = DocumentoCliente::findOrFail($id);
        
        if (!Storage::disk('public')->exists($documentoCliente->ruta_archivo)) {
            abort(404, 'Archivo no encontrado');
        }

        return Storage::disk('public')->download($documentoCliente->ruta_archivo, $documentoCliente->nombre_archivo);
    }
}
