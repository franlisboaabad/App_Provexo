@extends('adminlte::page')

@section('title', 'Detalle del Documento')

@section('content_header')
    <h1>Detalle del Documento</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="card-tools">
                <a href="{{ route('admin.documentos-clientes.download', $documentoCliente->id) }}"
                   class="btn btn-info btn-sm"
                   target="_blank">
                    <i class="fas fa-download"></i> Descargar
                </a>
                @can('admin.documentos-clientes.edit')
                    <a href="{{ route('admin.documentos-clientes.edit', $documentoCliente->id) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                @endcan
                @can('admin.documentos-clientes.destroy')
                    <form action="{{ route('admin.documentos-clientes.destroy', $documentoCliente->id) }}"
                          method="POST"
                          style="display: inline-block;"
                          onsubmit="return confirm('¿Está seguro de eliminar este documento? Esta acción no se puede deshacer.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </form>
                @endcan
                <a href="{{ route('admin.documentos-clientes.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Información del Documento</h5>
                    <hr>
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">ID:</th>
                            <td>{{ $documentoCliente->id }}</td>
                        </tr>
                        <tr>
                            <th>Título:</th>
                            <td><strong>{{ $documentoCliente->titulo }}</strong></td>
                        </tr>
                        <tr>
                            <th>Tipo:</th>
                            <td>
                                @php
                                    $tipos = [
                                        'factura' => ['badge' => 'success', 'text' => 'Factura'],
                                        'contrato' => ['badge' => 'primary', 'text' => 'Contrato'],
                                        'garantia' => ['badge' => 'info', 'text' => 'Garantía'],
                                        'orden_compra' => ['badge' => 'warning', 'text' => 'Orden Compra'],
                                        'otro' => ['badge' => 'secondary', 'text' => 'Otro']
                                    ];
                                    $tipo = $tipos[$documentoCliente->tipo_documento] ?? $tipos['otro'];
                                @endphp
                                <span class="badge badge-{{ $tipo['badge'] }}">{{ $tipo['text'] }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Número de Documento:</th>
                            <td>{{ $documentoCliente->numero_documento ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Fecha del Documento:</th>
                            <td>{{ $documentoCliente->fecha_documento ? $documentoCliente->fecha_documento->format('d/m/Y') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Estado:</th>
                            <td>
                                @if($documentoCliente->activo)
                                    <span class="badge badge-success">Activo</span>
                                @else
                                    <span class="badge badge-danger">Inactivo</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="col-md-6">
                    <h5>Información Relacionada</h5>
                    <hr>
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">Cliente:</th>
                            <td>
                                @if($documentoCliente->cliente)
                                    <a href="{{ route('admin.clientes.show', $documentoCliente->cliente) }}">
                                        {{ $documentoCliente->cliente->user->name ?? 'N/A' }}
                                    </a>
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Cotización:</th>
                            <td>
                                @if($documentoCliente->cotizacion)
                                    <a href="{{ route('admin.cotizaciones.show', $documentoCliente->cotizacion) }}">
                                        {{ $documentoCliente->cotizacion->numero_cotizacion }}
                                    </a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Subido por:</th>
                            <td>{{ $documentoCliente->usuario->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Fecha de Subida:</th>
                            <td>{{ $documentoCliente->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Última Actualización:</th>
                            <td>{{ $documentoCliente->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <h5>Archivo</h5>
                    <hr>
                    <div class="alert alert-info">
                        <i class="fas fa-file"></i>
                        <strong>Nombre del archivo:</strong> {{ $documentoCliente->nombre_archivo }}
                        <br>
                        <a href="{{ route('admin.documentos-clientes.download', $documentoCliente->id) }}"
                           class="btn btn-primary btn-sm mt-2"
                           target="_blank">
                            <i class="fas fa-download"></i> Descargar Archivo
                        </a>
                    </div>
                </div>
            </div>

            @if($documentoCliente->observaciones)
            <div class="row mt-3">
                <div class="col-md-12">
                    <h5>Observaciones</h5>
                    <hr>
                    <p>{{ $documentoCliente->observaciones }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
@stop

