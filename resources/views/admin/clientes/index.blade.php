@extends('adminlte::page')

@section('title', 'Lista de Clientes')

@section('content_header')
    <h1>Lista de Clientes</h1>
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
            @can('admin.clientes.create')
                <a href="{{ route('admin.clientes.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Nuevo Cliente
                </a>
            @endcan
        </div>
        <div class="card-body">
            <table id="clientesTable" class="table table-bordered table-striped table-hover">
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
                    @forelse ($clientes as $cliente)
                        <tr>
                            <td>{{ $cliente->id }}</td>
                            <td>{{ $cliente->user->name }}</td>
                            <td>{{ $cliente->user->email }}</td>
                            <td>{{ $cliente->celular ?? 'N/A' }}</td>
                            <td>{{ $cliente->empresa ?? 'N/A' }}</td>
                            <td>{{ $cliente->ruc ?? 'N/A' }}</td>
                            <td>
                                @can('admin.clientes.show')
                                    <a href="{{ route('admin.clientes.show', $cliente) }}"
                                       class="btn btn-info btn-sm"
                                       title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                @endcan

                                @can('admin.clientes.edit')
                                    <a href="{{ route('admin.clientes.edit', $cliente) }}"
                                       class="btn btn-warning btn-sm"
                                       title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endcan

                                @can('admin.clientes.destroy')
                                    <form action="{{ route('admin.clientes.destroy', $cliente) }}"
                                          method="POST"
                                          style="display: inline-block;"
                                          onsubmit="return confirm('¿Está seguro de eliminar este cliente? Esta acción no se puede deshacer.');">
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
                            <td colspan="7" class="text-center">No hay clientes registrados</td>
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
            $('#clientesTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json"
                },
                "pageLength": 25,
                "order": [[0, "desc"]]
            });
        });
    </script>
@stop
