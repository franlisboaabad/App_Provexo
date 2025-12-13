@extends('adminlte::page')

@section('title', 'Reportes - Dashboard')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-chart-bar text-primary"></i> Dashboard de Reportes
        </h1>
        <div class="d-flex align-items-center gap-2">
            <form method="GET" action="{{ route('admin.reportes.dashboard') }}" class="d-flex align-items-center gap-2">
                <label for="fecha_inicio" class="mb-0">Desde:</label>
                <input type="date"
                       name="fecha_inicio"
                       id="fecha_inicio"
                       class="form-control form-control-sm"
                       value="{{ $fechaInicio }}"
                       style="width: auto;">
                <label for="fecha_fin" class="mb-0">Hasta:</label>
                <input type="date"
                       name="fecha_fin"
                       id="fecha_fin"
                       class="form-control form-control-sm"
                       value="{{ $fechaFin }}"
                       style="width: auto;">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
            </form>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <!-- Gráfico de Ventas Mensuales -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line text-success"></i> Ventas Mensuales
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="ventasMensuales" style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Gráfico de Estados de Cotizaciones -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie text-info"></i> Distribución de Estados de Cotizaciones
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="estadosCotizaciones" style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico de Top Clientes -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar text-warning"></i> Top 5 Clientes por Monto Vendido
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="topClientes" style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        $(function () {
            // Datos de ventas mensuales
            const ventasMensuales = @json($ventasMensuales);

            // Gráfico de Ventas Mensuales
            const ctxVentas = document.getElementById('ventasMensuales').getContext('2d');
            new Chart(ctxVentas, {
                type: 'line',
                data: {
                    labels: ventasMensuales.meses,
                    datasets: [
                        {
                            label: 'Monto Vendido (S/)',
                            data: ventasMensuales.montos_vendidos,
                            borderColor: '#28a745',
                            backgroundColor: 'rgba(40, 167, 69, 0.1)',
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Adelantos (S/)',
                            data: ventasMensuales.adelantos,
                            borderColor: '#17a2b8',
                            backgroundColor: 'rgba(23, 162, 184, 0.1)',
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Restantes (S/)',
                            data: ventasMensuales.restantes,
                            borderColor: '#ffc107',
                            backgroundColor: 'rgba(255, 193, 7, 0.1)',
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
                                callback: function(value) {
                                    return 'S/ ' + value.toLocaleString('es-PE', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                }
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
                            intersect: false,
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': S/ ' + context.parsed.y.toLocaleString('es-PE', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                }
                            }
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    }
                }
            });

            // Datos de estados de cotizaciones
            const estadosCotizaciones = @json($estadosCotizaciones);

            // Gráfico de Estados de Cotizaciones (Pie Chart)
            const ctxEstados = document.getElementById('estadosCotizaciones').getContext('2d');
            new Chart(ctxEstados, {
                type: 'doughnut',
                data: {
                    labels: estadosCotizaciones.labels,
                    datasets: [{
                        data: estadosCotizaciones.datos,
                        backgroundColor: estadosCotizaciones.colores,
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'right'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return label + ': ' + value + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });

            // Datos de top clientes
            const topClientes = @json($topClientes);

            // Gráfico de Top Clientes (Bar Chart)
            const ctxClientes = document.getElementById('topClientes').getContext('2d');
            new Chart(ctxClientes, {
                type: 'bar',
                data: {
                    labels: topClientes.nombres,
                    datasets: [{
                        label: 'Monto Vendido (S/)',
                        data: topClientes.montos,
                        backgroundColor: 'rgba(255, 193, 7, 0.8)',
                        borderColor: '#ffc107',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y', // Barras horizontales
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'S/ ' + value.toLocaleString('es-PE', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Monto Vendido: S/ ' + context.parsed.x.toLocaleString('es-PE', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@stop

