@extends('adminlte::page')

@section('title', 'Lista de Usuarios')

@section('content_header')
    <h1>Lista de Usuarios</h1>
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
            @can('admin.usuarios.index')
                <span class="text-muted">Total de usuarios: {{ $usuarios->count() }}</span>
            @endcan
        </div>
        <div class="card-body">
            <table id="usuariosTable" class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Roles</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($usuarios as $usuario)
                        <tr>
                            <td>{{ $usuario->id }}</td>
                            <td>{{ $usuario->name }}</td>
                            <td>{{ $usuario->email }}</td>
                            <td>
                                @foreach($usuario->roles as $role)
                                    <span class="badge badge-primary">{{ $role->name }}</span>
                                @endforeach
                                @if($usuario->roles->isEmpty())
                                    <span class="text-muted">Sin roles</span>
                                @endif
                            </td>
                            <td>
                                @if($usuario->activo)
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
                                        @can('admin.usuarios.edit')
                                            <a class="dropdown-item" href="{{ route('admin.usuarios.edit', $usuario) }}">
                                                <i class="fas fa-edit text-warning"></i> Editar Usuario y Roles
                                            </a>
                                        @endcan
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No hay usuarios registrados</td>
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
            $('#usuariosTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json"
                },
                "pageLength": 25,
                "order": [[0, "desc"]],
                "columnDefs": [
                    { "orderable": false, "targets": [5] } // Acciones no ordenable
                ]
            });
        });
    </script>
@stop
