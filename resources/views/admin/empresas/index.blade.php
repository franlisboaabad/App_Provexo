@extends('adminlte::page')

@section('title', 'Lista de Empresas')

@section('content_header')
    <h1>Lista de Empresas</h1>
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
            @can('admin.empresas.create')
                <a href="{{ route('admin.empresas.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Nueva Empresa
                </a>
            @endcan
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Razón Social</th>
                        <th>Nombre Comercial</th>
                        <th>RUC</th>
                        <th>Email</th>
                        <th>Estado</th>
                        <th>Principal</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($empresas as $empresa)
                        <tr>
                            <td>{{ $empresa->id }}</td>
                            <td><strong>{{ $empresa->razon_social }}</strong></td>
                            <td>{{ $empresa->nombre_comercial ?? 'N/A' }}</td>
                            <td>{{ $empresa->ruc }}</td>
                            <td>{{ $empresa->email ?? 'N/A' }}</td>
                            <td>
                                @if($empresa->activo)
                                    <span class="badge badge-success">Activa</span>
                                @else
                                    <span class="badge badge-danger">Inactiva</span>
                                @endif
                            </td>
                            <td>
                                @if($empresa->es_principal)
                                    <span class="badge badge-primary"><i class="fas fa-star"></i> Principal</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @can('admin.empresas.show')
                                    <a href="{{ route('admin.empresas.show', $empresa) }}"
                                       class="btn btn-info btn-sm"
                                       title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                @endcan

                                @can('admin.empresas.edit')
                                    <a href="{{ route('admin.empresas.edit', $empresa) }}"
                                       class="btn btn-warning btn-sm"
                                       title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endcan

                                @can('admin.empresas.destroy')
                                    <form action="{{ route('admin.empresas.destroy', $empresa) }}"
                                          method="POST"
                                          style="display: inline-block;"
                                          onsubmit="return confirm('¿Está seguro de eliminar esta empresa? Esta acción no se puede deshacer.');">
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
                            <td colspan="8" class="text-center">No hay empresas registradas</td>
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
    </script>
@stop

