@extends('adminlte::page')

@section('title', 'Configuración de Documentos de Cotización')

@section('content_header')
    <h1>Configuración de Documentos de Cotización</h1>
@stop

@section('content')
    @if(session('success'))
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
            <h5 class="mb-0">
                <i class="fas fa-cog"></i> Editar Configuración
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.configuracion-documentos.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="observaciones">
                        <strong>OBSERVACIONES</strong>
                        <small class="text-muted">(Se mostrarán en el PDF de las cotizaciones)</small>
                    </label>
                    <textarea
                        class="form-control"
                        id="observaciones"
                        name="observaciones"
                        rows="10"
                        placeholder="Ingrese las observaciones que aparecerán en las cotizaciones...">{{ old('observaciones', $configuracion->observaciones) }}</textarea>
                    <small class="form-text text-muted">
                        Puede usar HTML para formatear el texto. Ejemplo: &lt;ul&gt;&lt;li&gt;Texto&lt;/li&gt;&lt;/ul&gt;
                    </small>
                </div>

                <div class="form-group">
                    <label for="condiciones_pago">
                        <strong>CONDICIONES DE PAGO</strong>
                        <small class="text-muted">(Se mostrarán en el PDF de las cotizaciones)</small>
                    </label>
                    <textarea
                        class="form-control"
                        id="condiciones_pago"
                        name="condiciones_pago"
                        rows="10"
                        placeholder="Ingrese las condiciones de pago que aparecerán en las cotizaciones...">{{ old('condiciones_pago', $configuracion->condiciones_pago) }}</textarea>
                    <small class="form-text text-muted">
                        Puede usar HTML para formatear el texto. Ejemplo: &lt;ul&gt;&lt;li&gt;Texto&lt;/li&gt;&lt;/ul&gt;
                    </small>
                </div>

                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Configuración
                    </button>
                    <a href="{{ route('admin.cotizaciones.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-info-circle"></i> Información
            </h5>
        </div>
        <div class="card-body">
            <p class="mb-2">
                <strong>Nota:</strong> Esta configuración se aplicará a todas las cotizaciones que se generen.
            </p>
            <p class="mb-0">
                <strong>Formato HTML permitido:</strong> Puede usar etiquetas HTML básicas como:
                <code>&lt;ul&gt;</code>, <code>&lt;li&gt;</code>, <code>&lt;strong&gt;</code>, <code>&lt;em&gt;</code>, etc.
            </p>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        // Auto-resize textareas
        document.querySelectorAll('textarea').forEach(textarea => {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
        });
    </script>
@stop

