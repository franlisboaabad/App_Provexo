@extends('adminlte::page')

@section('title', 'Dashboard')


@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-tachometer-alt text-primary"></i> Dashboard
        </h1>
    </div>
@stop

@section('content')
    <!-- Cards de Estadísticas -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $usuariosActivos }}</h3>
                    <p>Usuarios Activos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('admin.usuarios.index') }}" class="small-box-footer">
                    Más información <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $cotizacionesPendientes }}</h3>
                    <p>Cotizaciones Pendientes</p>
                </div>
                <div class="icon">
                    <i class="far fa-clock"></i>
                </div>
                <a href="{{ route('admin.cotizaciones.index') }}?estado=pendiente" class="small-box-footer">
                    Más información <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $totalCotizaciones }}</h3>
                    <p>Total Cotizaciones</p>
                </div>
                <div class="icon">
                    <i class="far fa-file-alt"></i>
                </div>
                <a href="{{ route('admin.cotizaciones.index') }}" class="small-box-footer">
                    Más información <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-purple">
                <div class="inner">
                    <h3>{{ number_format($totalProductos, 0, ',', '.') }}</h3>
                    <p>Productos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-cube"></i>
                </div>
                <a href="{{ route('admin.productos.index') }}" class="small-box-footer">
                    Más información <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>


    <div class="row">
        <!-- Gráfico de Actividad Semanal -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line text-primary"></i> Actividad Semanal
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="actividadSemanal" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>

        <!-- Actividad Reciente -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history text-primary"></i> Actividad Reciente
                    </h3>
                </div>
                <div class="card-body p-0">
                    <ul class="products-list product-list-in-card pl-2 pr-2">
                        @if($cotizacionesRecientes->count() > 0)
                            @foreach($cotizacionesRecientes->take(3) as $cotizacion)
                            <li class="item">
                                <div class="product-img">
                                    <i class="far fa-file-alt fa-2x text-info"></i>
                                </div>
                                <div class="product-info">
                                    <a href="{{ route('admin.cotizaciones.show', $cotizacion) }}" class="product-title">
                                        Cotización #{{ $cotizacion->numero_cotizacion }}
                                        <span class="badge badge-{{ $cotizacion->estado == 'pendiente' ? 'warning' : ($cotizacion->estado == 'aprobada' ? 'success' : 'secondary') }} float-right">
                                            {{ ucfirst($cotizacion->estado) }}
                                        </span>
                                    </a>
                                    <span class="product-description">
                                        Cliente: {{ $cotizacion->cliente->user->name ?? 'N/A' }}
                                        <br>
                                        <small class="text-muted">{{ $cotizacion->created_at->diffForHumans() }}</small>
                                    </span>
                                </div>
                            </li>
                            @endforeach
                        @else
                            <li class="item">
                                <div class="product-info">
                                    <span class="product-description text-muted">
                                        No hay cotizaciones recientes
                                    </span>
                                </div>
                            </li>
                        @endif

                        @if($productosRecientes->count() > 0)
                            @foreach($productosRecientes->take(2) as $producto)
                            <li class="item">
                                <div class="product-img">
                                    <i class="fas fa-cube fa-2x text-purple"></i>
                                </div>
                                <div class="product-info">
                                    <a href="{{ route('admin.productos.show', $producto) }}" class="product-title">
                                        {{ $producto->descripcion }}
                                    </a>
                                    <span class="product-description">
                                        Código: {{ $producto->codigo_producto }}
                                        <br>
                                        <small class="text-muted">{{ $producto->created_at->diffForHumans() }}</small>
                                    </span>
                                </div>
                            </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <style>
        .bg-purple {
            background-color: #6f42c1 !important;
        }
        .btn-purple {
            background-color: #6f42c1;
            border-color: #6f42c1;
            color: #fff;
        }
        .btn-purple:hover {
            background-color: #5a32a3;
            border-color: #5a32a3;
            color: #fff;
        }
        .small-box .icon {
            color: rgba(0,0,0,.15);
        }
        .btn-lg {
            padding: 20px;
            font-size: 14px;
        }
        .btn-lg i {
            font-size: 32px;
            margin-bottom: 10px;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        $(function () {
            // Datos de actividad semanal
            const actividadSemanal = @json($actividadSemanal);

            const labels = actividadSemanal.map(item => item.dia);
            const datosCotizaciones = actividadSemanal.map(item => item.cotizaciones);
            const datosProductos = actividadSemanal.map(item => item.productos);
            const datosUsuarios = actividadSemanal.map(item => item.usuarios);

            // Calcular total de actividades para cada día
            const datosTotales = actividadSemanal.map(item =>
                item.cotizaciones + item.productos + item.usuarios
            );

            // Crear gráfico
            const ctx = document.getElementById('actividadSemanal').getContext('2d');
            const chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Total Actividad',
                            data: datosTotales,
                            borderColor: '#007bff',
                            backgroundColor: 'rgba(0, 123, 255, 0.1)',
                            tension: 0.4,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    }
                }
            });
        });
    </script>
@stop

@section('footer')
    <div class="float-right d-none d-sm-block">
        <b>Versión</b> 1.0.0
    </div>
    <strong>Copyright &copy; {{ date('Y') }} <a href="{{ env('APP_URL') }}">Provexo+</a>.</strong> Todos los derechos reservados.
@endsection
