@extends('adminlte::page')

@section('title', 'Detalle de Cotización')

@section('content_header')
    <h1>Cotización: {{ $cotizacione->numero_cotizacion }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <a href="{{ route('admin.cotizaciones.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Volver a la lista
                    </a>
                    @can('admin.cotizaciones.edit')
                        <a href="{{ route('admin.cotizaciones.edit', $cotizacione) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Editar Cotización
                        </a>
                    @endcan
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Información del Cliente</h5>
                            <hr>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Cliente:</th>
                                    <td><strong>{{ $cotizacione->cliente->user->name ?? 'N/A' }}</strong></td>
                                </tr>
                                @if($cotizacione->cliente->empresa)
                                    <tr>
                                        <th>Empresa:</th>
                                        <td>{{ $cotizacione->cliente->empresa }}</td>
                                    </tr>
                                @endif
                                @if($cotizacione->cliente->ruc)
                                    <tr>
                                        <th>RUC:</th>
                                        <td>{{ $cotizacione->cliente->ruc }}</td>
                                    </tr>
                                @endif
                                @if($cotizacione->cliente->celular)
                                    <tr>
                                        <th>Celular:</th>
                                        <td>{{ $cotizacione->cliente->celular }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h5>Información de la Cotización</h5>
                            <hr>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Número:</th>
                                    <td><strong>{{ $cotizacione->numero_cotizacion }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Fecha Emisión:</th>
                                    <td>{{ $cotizacione->fecha_emision->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Fecha Vencimiento:</th>
                                    <td>{{ $cotizacione->fecha_vencimiento ? $cotizacione->fecha_vencimiento->format('d/m/Y') : 'Sin fecha de vencimiento' }}</td>
                                </tr>
                                <tr>
                                    <th>Estado:</th>
                                    <td>
                                        @if($cotizacione->estado == 'aprobada')
                                            <span class="badge badge-success">Aprobada</span>
                                        @elseif($cotizacione->estado == 'rechazada')
                                            <span class="badge badge-danger">Rechazada</span>
                                        @elseif($cotizacione->estado == 'vencida')
                                            <span class="badge badge-warning">Vencida</span>
                                        @else
                                            <span class="badge badge-info">Pendiente</span>
                                        @endif
                                    </td>
                                </tr>
                                @if($cotizacione->observaciones)
                                    <tr>
                                        <th>Observaciones:</th>
                                        <td>{{ $cotizacione->observaciones }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <hr>

                    <h5>Productos</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Código</th>
                                    <th>Descripción</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-right">Precio Unit.</th>
                                    <th class="text-center">Descuento %</th>
                                    <th class="text-center">Impuesto %</th>
                                    <th class="text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cotizacione->productos as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><strong>{{ $item->producto->codigo_producto }}</strong></td>
                                        <td>{{ $item->producto->descripcion }}</td>
                                        <td class="text-center">{{ $item->cantidad }} {{ $item->producto->unidad_medida }}</td>
                                        <td class="text-right">S/ {{ number_format($item->precio_unitario, 2) }}</td>
                                        <td class="text-center">{{ $item->descuento > 0 ? number_format($item->descuento, 2) . '%' : '-' }}</td>
                                        <td class="text-center">{{ $item->impuesto > 0 ? number_format($item->impuesto, 2) . '%' : '-' }}</td>
                                        <td class="text-right"><strong>S/ {{ number_format($item->subtotal, 2) }}</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="7" class="text-right"><strong>Subtotal:</strong></td>
                                    <td class="text-right"><strong>S/ {{ number_format($cotizacione->subtotal, 2) }}</strong></td>
                                </tr>
                                @if($cotizacione->descuento > 0)
                                    <tr>
                                        <td colspan="7" class="text-right"><strong>Descuento:</strong></td>
                                        <td class="text-right"><strong class="text-danger">-S/ {{ number_format($cotizacione->descuento, 2) }}</strong></td>
                                    </tr>
                                @endif
                                @if($cotizacione->impuesto_total > 0)
                                    <tr>
                                        <td colspan="7" class="text-right"><strong>Impuesto Total:</strong></td>
                                        <td class="text-right"><strong>S/ {{ number_format($cotizacione->impuesto_total, 2) }}</strong></td>
                                    </tr>
                                @endif
                                <tr class="table-primary">
                                    <td colspan="7" class="text-right"><strong>TOTAL:</strong></td>
                                    <td class="text-right"><strong>S/ {{ number_format($cotizacione->total, 2) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log('Cotización detalle'); </script>
@stop

