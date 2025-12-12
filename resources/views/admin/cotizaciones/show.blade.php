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
                    <a href="{{ route('admin.cotizaciones.pdf', $cotizacione) }}" class="btn btn-danger btn-sm" target="_blank">
                        <i class="fas fa-file-pdf"></i> Descargar PDF
                    </a>
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

                    @php
                        // Verificar si hay productos con datos de flete
                        $productosConFlete = $cotizacione->productos->filter(function($item) {
                            return !is_null($item->peso_unidad) && !is_null($item->flete_tonelada);
                        });
                        $tieneFletes = $productosConFlete->count() > 0;

                        // Calcular totales de fletes
                        $totalKg = $productosConFlete->sum('total_kg') ?? 0;
                        $totalFlete = $productosConFlete->sum('flete_total') ?? 0;
                        $totalMargen = $productosConFlete->sum('margen_total') ?? 0;
                        $totalCostoFlete = $productosConFlete->sum('costo_mas_flete') ?? 0;
                    @endphp

                    @if($tieneFletes && !auth()->user()->hasRole('Cliente'))
                        <hr>
                        <h5><i class="fas fa-truck"></i> Resumen de Fletes y Margen</h5>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Código</th>
                                                <th>Descripción</th>
                                                <th class="text-center">Cantidad</th>
                                                <th class="text-right">Precio Base</th>
                                                <th class="text-center">Peso x Unidad (kg)</th>
                                                <th class="text-center">Flete x Tonelada (S/)</th>
                                                <th class="text-center">% Margen</th>
                                                <th class="text-right">Flete Unit.</th>
                                                <th class="text-right">Costo + Flete</th>
                                                <th class="text-right">Total KG</th>
                                                <th class="text-right">Margen Total</th>
                                                <th class="text-right">Flete Total (S/)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($productosConFlete as $index => $item)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td><strong>{{ $item->producto->codigo_producto }}</strong></td>
                                                    <td>{{ $item->producto->descripcion }}</td>
                                                    <td class="text-center">{{ $item->cantidad }}</td>
                                                    <td class="text-right">
                                                        @php
                                                            $precioBaseMostrar = $item->precio_base_cotizacion ?? $item->producto->precio_base;
                                                        @endphp
                                                        S/ {{ number_format($precioBaseMostrar, 2) }}
                                                        @if($item->precio_base_cotizacion && abs($item->precio_base_cotizacion - $item->producto->precio_base) > 0.01)
                                                            <br><small class="text-info" title="Precio base modificado en esta cotización">
                                                                <i class="fas fa-info-circle"></i> Base: S/ {{ number_format($item->producto->precio_base, 2) }}
                                                            </small>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">{{ number_format($item->peso_unidad, 4) }}</td>
                                                    <td class="text-center">S/ {{ number_format($item->flete_tonelada, 2) }}</td>
                                                    <td class="text-center">{{ number_format($item->margen_porcentaje, 2) }}%</td>
                                                    <td class="text-right">S/ {{ number_format($item->flete_unitario, 4) }}</td>
                                                    <td class="text-right">S/ {{ number_format($item->costo_mas_flete, 2) }}</td>
                                                    <td class="text-right">{{ number_format($item->total_kg, 4) }}</td>
                                                    <td class="text-right">S/ {{ number_format($item->margen_total, 2) }}</td>
                                                    <td class="text-right">S/ {{ number_format($item->flete_total, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="table-info">
                                            <tr>
                                                <td colspan="10" class="text-right"><strong>RESUMEN TOTAL:</strong></td>
                                                <td class="text-right"><strong>{{ number_format($totalKg, 4) }} kg</strong></td>
                                                <td class="text-right"><strong>S/ {{ number_format($totalMargen, 2) }}</strong></td>
                                                <td class="text-right"><strong>S/ {{ number_format($totalFlete, 2) }}</strong></td>
                                            </tr>
                                            <tr>
                                                <td colspan="9" class="text-right"><strong>Total Costo + Flete:</strong></td>
                                                <td class="text-right" colspan="4"><strong>S/ {{ number_format($totalCostoFlete, 2) }}</strong></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
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
    <script> console.log('Cotización detalle'); </script>
@stop

