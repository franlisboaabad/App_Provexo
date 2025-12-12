@extends('adminlte::page')

@section('title', 'Detalle del Cliente')

@section('content_header')
    <h1>Detalle del Cliente</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="card-tools">
                @can('admin.clientes.edit')
                    <a href="{{ route('admin.clientes.edit', $cliente) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                @endcan
                <a href="{{ route('admin.clientes.index') }}" class="btn btn-secondary btn-sm">
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
                            <td>{{ $cliente->user->id }}</td>
                        </tr>
                        <tr>
                            <th>Nombre:</th>
                            <td>{{ $cliente->user->name }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $cliente->user->email }}</td>
                        </tr>
                        <tr>
                            <th>Estado:</th>
                            <td>
                                @if($cliente->user->activo)
                                    <span class="badge badge-success">Activo</span>
                                @else
                                    <span class="badge badge-danger">Inactivo</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Registrado:</th>
                            <td>{{ $cliente->user->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Última actualización:</th>
                            <td>{{ $cliente->user->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>

                <div class="col-md-6">
                    <h5>Información del Cliente</h5>
                    <hr>
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">ID Cliente:</th>
                            <td>{{ $cliente->id }}</td>
                        </tr>
                        <tr>
                            <th>Celular:</th>
                            <td>{{ $cliente->celular ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Empresa:</th>
                            <td>{{ $cliente->empresa ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>RUC:</th>
                            <td>{{ $cliente->ruc ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Roles:</th>
                            <td>
                                @foreach($cliente->user->roles as $role)
                                    <span class="badge badge-primary">{{ $role->name }}</span>
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <th>Creado:</th>
                            <td>{{ $cliente->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <hr>

            <!-- Historial de Cotizaciones -->
            <div class="row">
                <div class="col-md-12">
                    <h5><i class="fas fa-file-invoice"></i> Historial de Cotizaciones</h5>
                    <hr>
                    @if($cotizaciones->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Número de Cotización</th>
                                        <th>Fecha Emisión</th>
                                        <th>Fecha Vencimiento</th>
                                        <th class="text-center">Estado</th>
                                        <th class="text-right">Subtotal</th>
                                        <th class="text-right">Descuento</th>
                                        <th class="text-right">Impuesto</th>
                                        <th class="text-right">Total</th>
                                        <th class="text-center">Productos</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cotizaciones as $index => $cotizacion)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td><strong>{{ $cotizacion->numero_cotizacion }}</strong></td>
                                            <td>{{ $cotizacion->fecha_emision->format('d/m/Y') }}</td>
                                            <td>
                                                {{ $cotizacion->fecha_vencimiento ? $cotizacion->fecha_vencimiento->format('d/m/Y') : 'Sin fecha' }}
                                            </td>
                                            <td class="text-center">
                                                @if($cotizacion->estado == 'aprobada')
                                                    <span class="badge badge-success">Aprobada</span>
                                                @elseif($cotizacion->estado == 'rechazada')
                                                    <span class="badge badge-danger">Rechazada</span>
                                                @elseif($cotizacion->estado == 'vencida')
                                                    <span class="badge badge-warning">Vencida</span>
                                                @else
                                                    <span class="badge badge-info">Pendiente</span>
                                                @endif
                                            </td>
                                            <td class="text-right">S/ {{ number_format($cotizacion->subtotal, 2) }}</td>
                                            <td class="text-right">
                                                @if($cotizacion->descuento > 0)
                                                    <span class="text-danger">-S/ {{ number_format($cotizacion->descuento, 2) }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-right">S/ {{ number_format($cotizacion->impuesto_total, 2) }}</td>
                                            <td class="text-right">
                                                <strong class="text-primary">S/ {{ number_format($cotizacion->total, 2) }}</strong>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-secondary">{{ $cotizacion->productos->count() }} producto(s)</span>
                                            </td>
                                            <td class="text-center">
                                                @can('admin.cotizaciones.show')
                                                    <a href="{{ route('admin.cotizaciones.show', $cotizacion) }}"
                                                       class="btn btn-sm btn-info"
                                                       title="Ver detalle">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endcan
                                                @can('admin.cotizaciones.pdf')
                                                    <a href="{{ route('admin.cotizaciones.pdf', $cotizacion) }}"
                                                       class="btn btn-sm btn-danger"
                                                       target="_blank"
                                                       title="Descargar PDF">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-info">
                                    <tr>
                                        <td colspan="5" class="text-right"><strong>RESUMEN TOTAL:</strong></td>
                                        <td class="text-right"><strong>S/ {{ number_format($cotizaciones->sum('subtotal'), 2) }}</strong></td>
                                        <td class="text-right"><strong class="text-danger">-S/ {{ number_format($cotizaciones->sum('descuento'), 2) }}</strong></td>
                                        <td class="text-right"><strong>S/ {{ number_format($cotizaciones->sum('impuesto_total'), 2) }}</strong></td>
                                        <td class="text-right"><strong class="text-primary">S/ {{ number_format($cotizaciones->sum('total'), 2) }}</strong></td>
                                        <td class="text-center"><strong>{{ $cotizaciones->sum(function($cot) { return $cot->productos->count(); }) }} producto(s)</strong></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Este cliente no tiene cotizaciones registradas.
                        </div>
                    @endif
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
        console.log('Vista de detalle de cliente cargada');
    </script>
@stop
