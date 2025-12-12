@extends('adminlte::page')

@section('title', 'Lista de Proveedores')

@section('content_header')
    <h1>Lista de Proveedores</h1>
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

    <div class="card">
        <div class="card-header">
            @can('admin.proveedores.create')
                <a href="{{ route('admin.proveedores.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Nuevo Proveedor
                </a>
            @endcan
        </div>
        <div class="card-body">
            <table id="proveedoresTable" class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Celular</th>
                        <th>Empresa</th>
                        <th>RUC</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($proveedores as $proveedor)
                        <tr>
                            <td>{{ $proveedor->id }}</td>
                            <td>{{ $proveedor->user->name }}</td>
                            <td>{{ $proveedor->user->email }}</td>
                            <td>{{ $proveedor->celular ?? 'N/A' }}</td>
                            <td>{{ $proveedor->empresa ?? 'N/A' }}</td>
                            <td>{{ $proveedor->ruc ?? 'N/A' }}</td>
                            <td>
                                @can('admin.proveedores.show')
                                    <a href="{{ route('admin.proveedores.show', $proveedor) }}"
                                       class="btn btn-info btn-sm"
                                       title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                @endcan

                                @can('admin.proveedores.edit')
                                    <a href="{{ route('admin.proveedores.edit', $proveedor) }}"
                                       class="btn btn-warning btn-sm"
                                       title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endcan

                                @can('admin.proveedores.destroy')
                                    <form action="{{ route('admin.proveedores.destroy', $proveedor) }}"
                                          method="POST"
                                          style="display: inline-block;"
                                          onsubmit="return confirm('¿Está seguro de eliminar este proveedor? Esta acción no se puede deshacer.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No hay proveedores registrados</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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

        // Inicializar DataTable
        $(document).ready(function() {
            $('#proveedoresTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json"
                },
                "pageLength": 25,
                "order": [[0, "desc"]]
            });
        });
    </script>
@stop

