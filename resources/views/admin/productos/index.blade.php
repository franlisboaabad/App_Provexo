@extends('adminlte::page')

@section('title', 'Lista de Productos')

@section('content_header')
    <h1>Lista de Productos</h1>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('errores') && count(session('errores')) > 0)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <h6><strong>Errores durante la importación:</strong></h6>
            <ul class="mb-0" style="max-height: 200px; overflow-y: auto;">
                @foreach (session('errores') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            @can('admin.productos.create')
                <a href="{{ route('admin.productos.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Nuevo Producto
                </a>
                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalImportar">
                    <i class="fas fa-file-upload"></i> Importar Productos
                </button>
            @endcan
        </div>
        <div class="card-body">
            <table id="productosTable" class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Proveedor</th>
                        <th>Precio Base</th>
                        <th>Precio Venta</th>
                        <th>Stock</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($productos as $producto)
                        <tr>
                            <td>{{ $producto->id }}</td>
                            <td><strong>{{ $producto->codigo_producto }}</strong></td>
                            <td>{{ $producto->descripcion }}</td>
                            <td>
                                @if($producto->proveedor && $producto->proveedor->user)
                                    {{ $producto->proveedor->user->name }}
                                @else
                                    <span class="text-muted">Sin proveedor</span>
                                @endif
                            </td>
                            <td>S/ {{ number_format($producto->precio_base, 2) }}</td>
                            <td><strong>S/ {{ number_format($producto->precio_venta, 2) }}</strong></td>
                            <td>
                                <span class="badge badge-{{ $producto->stock > 0 ? 'success' : 'danger' }}">
                                    {{ $producto->stock }} {{ $producto->unidad_medida }}
                                </span>
                            </td>
                            <td>
                                @if($producto->activo)
                                    <span class="badge badge-success">Activo</span>
                                @else
                                    <span class="badge badge-danger">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-cog"></i> Acciones
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        @can('admin.productos.show')
                                            <a class="dropdown-item" href="{{ route('admin.productos.show', $producto) }}">
                                                <i class="fas fa-eye text-info"></i> Ver Detalle
                                            </a>
                                            <a class="dropdown-item" href="{{ route('admin.productos.historial-precios', $producto) }}">
                                                <i class="fas fa-history text-secondary"></i> Historial de Precios
                                            </a>
                                        @endcan
                                        @can('admin.productos.edit')
                                            <a class="dropdown-item" href="{{ route('admin.productos.edit', $producto) }}">
                                                <i class="fas fa-edit text-warning"></i> Editar
                                            </a>
                                        @endcan
                                        @can('admin.productos.destroy')
                                            <div class="dropdown-divider"></div>
                                            <form action="{{ route('admin.productos.destroy', $producto) }}"
                                                  method="POST"
                                                  style="display: inline-block;"
                                                  onsubmit="return confirm('¿Está seguro de eliminar este producto? Esta acción no se puede deshacer.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger" style="border: none; background: none; width: 100%; text-align: left; padding: 0.25rem 1.5rem;">
                                                    <i class="fas fa-trash"></i> Eliminar
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No hay productos registrados</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal para Importar Productos -->
    <div class="modal fade" id="modalImportar" tabindex="-1" role="dialog" aria-labelledby="modalImportarLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalImportarLabel">
                        <i class="fas fa-file-upload"></i> Importar Productos desde CSV
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.productos.import') }}" method="POST" enctype="multipart/form-data" id="formImportar">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Instrucciones:</h6>
                            <ul class="mb-0">
                                <li>El archivo puede estar en formato <strong>CSV</strong> o <strong>Excel (.xlsx, .xls)</strong></li>
                                <li>La primera fila debe contener los encabezados de las columnas</li>
                                <li>Columnas requeridas: <code>codigo_producto</code>, <code>descripcion</code>, <code>precio_base</code>, <code>precio_venta</code>, <code>impuesto</code></li>
                                <li>Columna opcional: <code>unidad_medida</code> (si no se especifica, se usará "unidad" por defecto)</li>
                                <li>Para CSV: el archivo debe usar <strong>coma (,) como separador</strong></li>
                                <li><strong>Nota:</strong> El proveedor_id será NULL por ahora</li>
                            </ul>
                        </div>

                        <div class="form-group">
                            <label for="archivo_csv">Seleccionar archivo (CSV o Excel) <span class="text-danger">*</span></label>
                            <div class="custom-file">
                                <input type="file"
                                       class="custom-file-input @error('archivo_csv') is-invalid @enderror"
                                       id="archivo_csv"
                                       name="archivo_csv"
                                       accept=".csv,.xlsx,.xls,text/csv,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                                       required>
                                <label class="custom-file-label" for="archivo_csv">Elegir archivo...</label>
                            </div>
                            @error('archivo_csv')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <small class="form-text text-muted">Tamaño máximo: 10MB. Formatos aceptados: CSV, XLSX, XLS</small>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Ejemplo de formato (CSV o Excel):</h6>
                            </div>
                            <div class="card-body">
                                <pre class="mb-0" style="font-size: 0.85rem;">codigo_producto,descripcion,precio_base,precio_venta,impuesto,unidad_medida
PROD001,Producto Ejemplo 1,10.50,15.00,18,unidad
PROD002,Producto Ejemplo 2,25.00,35.00,18,kg
PROD003,Producto Ejemplo 3,5.00,8.50,0,litro</pre>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-upload"></i> Importar Productos
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);

        // Mostrar nombre del archivo seleccionado
        $('#archivo_csv').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName || 'Elegir archivo...');
        });

        // Limpiar modal al cerrar
        $('#modalImportar').on('hidden.bs.modal', function() {
            $('#formImportar')[0].reset();
            $('#archivo_csv').next('.custom-file-label').html('Elegir archivo...');
        });

        // Inicializar DataTable
        $(document).ready(function() {
            $('#productosTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json"
                },
                "pageLength": 25,
                "order": [[0, "desc"]],
                "autoWidth": false,
                "columnDefs": [
                    { "orderable": false, "targets": [8] } // Acciones no ordenable
                ]
            });
        });
    </script>
@stop

