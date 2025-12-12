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

    <div class="row">
        <div class="col-md-4">
            <!-- Box de Cotizaciones -->
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalCotizaciones }}</h3>
                    <p>Mis Cotizaciones</p>
                </div>
                <div class="icon">
                    <i class="far fa-file-alt"></i>
                </div>
                <a href="{{ route('admin.cotizaciones.index') }}" class="small-box-footer">
                    Ver todas mis cotizaciones <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        @if($cotizacionesPendientes > 0)
        <div class="col-md-4">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $cotizacionesPendientes }}</h3>
                    <p>Cotizaciones Pendientes</p>
                </div>
                <div class="icon">
                    <i class="far fa-clock"></i>
                </div>
                <a href="{{ route('admin.cotizaciones.index') }}?estado=pendiente" class="small-box-footer">
                    Ver cotizaciones pendientes <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        @endif

    </div>



    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle text-primary"></i> Bienvenido
                    </h3>
                </div>
                <div class="card-body">
                    <p>Bienvenido a tu panel de cliente. Aquí puedes ver el resumen de tus cotizaciones.</p>
                    <p>Utiliza el menú lateral para navegar por las diferentes secciones disponibles.</p>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <style>
        .small-box .icon {
            color: rgba(0,0,0,.15);
        }
    </style>
@stop

@section('js')
@stop

