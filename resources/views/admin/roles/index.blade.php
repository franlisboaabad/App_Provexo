@extends('adminlte::page')

@section('title', 'Lista de Roles')

@section('content_header')
    <h1>Lista de Roles</h1>
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
            @can('admin.roles.create')
                <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Nuevo Rol
                </a>
            @endcan
        </div>
        <div class="card-body">
            <table id="rolesTable" class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre del Rol</th>
                        <th>Permisos</th>
                        <th>Usuarios</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($roles as $role)
                        <tr>
                            <td>{{ $role->id }}</td>
                            <td>
                                <strong>{{ $role->name }}</strong>
                            </td>
                            <td>
                                @if($role->permissions->count() > 0)
                                    <span class="badge badge-info">{{ $role->permissions->count() }} permiso(s)</span>
                                    <small class="d-block text-muted mt-1">
                                        {{ Str::limit($role->permissions->pluck('name')->implode(', '), 50) }}
                                    </small>
                                @else
                                    <span class="text-muted">Sin permisos</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-success">{{ $role->users->count() }} usuario(s)</span>
                            </td>
                            <td>
                                @can('admin.roles.edit')
                                    <a href="{{ route('admin.roles.edit', $role) }}"
                                       class="btn btn-warning btn-sm"
                                       title="Editar rol y permisos">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No hay roles registrados</td>
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
            $('#rolesTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json"
                },
                "pageLength": 25,
                "order": [[0, "desc"]]
            });
        });
    </script>
@stop
