@extends('adminlte::page')

@section('title', 'Detalle del Proveedor')

@section('content_header')
    <h1>Detalle del Proveedor</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="card-tools">
                @can('admin.proveedores.edit')
                    <a href="{{ route('admin.proveedores.edit', $proveedore) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                @endcan
                <a href="{{ route('admin.proveedores.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Información del Usuario</h5>
                    <hr>
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">ID:</th>
                            <td>{{ $proveedore->user->id }}</td>
                        </tr>
                        <tr>
                            <th>Nombre:</th>
                            <td>{{ $proveedore->user->name }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $proveedore->user->email }}</td>
                        </tr>
                        <tr>
                            <th>Estado:</th>
                            <td>
                                @if($proveedore->user->activo)
                                    <span class="badge badge-success">Activo</span>
                                @else
                                    <span class="badge badge-danger">Inactivo</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Registrado:</th>
                            <td>{{ $proveedore->user->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Última actualización:</th>
                            <td>{{ $proveedore->user->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>

                <div class="col-md-6">
                    <h5>Información del Proveedor</h5>
                    <hr>
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">ID Proveedor:</th>
                            <td>{{ $proveedore->id }}</td>
                        </tr>
                        <tr>
                            <th>Celular:</th>
                            <td>{{ $proveedore->celular ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Empresa:</th>
                            <td>{{ $proveedore->empresa ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>RUC:</th>
                            <td>{{ $proveedore->ruc ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Roles:</th>
                            <td>
                                @foreach($proveedore->user->roles as $role)
                                    <span class="badge badge-primary">{{ $role->name }}</span>
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <th>Creado:</th>
                            <td>{{ $proveedore->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        console.log('Vista de detalle de proveedor cargada');
    </script>
@stop

