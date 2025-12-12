@extends('adminlte::page')

@section('title', 'Registrar Documento de Cliente')

@section('content_header')
    <h1>Registrar Nuevo Documento</h1>
@stop

@section('content')
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
        <div class="card-body">
            <form action="{{ route('admin.documentos-clientes.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="cliente_id">Cliente <span class="text-danger">*</span></label>
                            <select class="form-control @error('cliente_id') is-invalid @enderror"
                                    id="cliente_id"
                                    name="cliente_id"
                                    required
                                    onchange="cargarCotizaciones(this.value)">
                                <option value="">Seleccione un cliente</option>
                                @foreach($clientes as $cliente)
                                    <option value="{{ $cliente->id }}"
                                            {{ old('cliente_id', $clienteSeleccionado?->id) == $cliente->id ? 'selected' : '' }}>
                                        {{ $cliente->user->name }} {{ $cliente->empresa ? '- ' . $cliente->empresa : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('cliente_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="cotizacion_id">Cotización (Opcional)</label>
                            <select class="form-control @error('cotizacion_id') is-invalid @enderror"
                                    id="cotizacion_id"
                                    name="cotizacion_id">
                                <option value="">Seleccione una cotización</option>
                                @if($cotizaciones)
                                    @foreach($cotizaciones as $cotizacion)
                                        <option value="{{ $cotizacion->id }}" {{ old('cotizacion_id') == $cotizacion->id ? 'selected' : '' }}>
                                            {{ $cotizacion->numero_cotizacion }} - {{ $cotizacion->fecha_emision->format('d/m/Y') }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('cotizacion_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <small class="form-text text-muted">Primero seleccione un cliente para ver sus cotizaciones</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="titulo">Título del Documento <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('titulo') is-invalid @enderror"
                                   id="titulo"
                                   name="titulo"
                                   value="{{ old('titulo') }}"
                                   required
                                   autofocus>
                            @error('titulo')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tipo_documento">Tipo de Documento <span class="text-danger">*</span></label>
                            <select class="form-control @error('tipo_documento') is-invalid @enderror"
                                    id="tipo_documento"
                                    name="tipo_documento"
                                    required>
                                <option value="">Seleccione un tipo</option>
                                <option value="factura" {{ old('tipo_documento') == 'factura' ? 'selected' : '' }}>Factura</option>
                                <option value="contrato" {{ old('tipo_documento') == 'contrato' ? 'selected' : '' }}>Contrato</option>
                                <option value="garantia" {{ old('tipo_documento') == 'garantia' ? 'selected' : '' }}>Garantía</option>
                                <option value="orden_compra" {{ old('tipo_documento') == 'orden_compra' ? 'selected' : '' }}>Orden de Compra</option>
                                <option value="otro" {{ old('tipo_documento') == 'otro' ? 'selected' : '' }}>Otro</option>
                            </select>
                            @error('tipo_documento')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="numero_documento">Número de Documento</label>
                            <input type="text"
                                   class="form-control @error('numero_documento') is-invalid @enderror"
                                   id="numero_documento"
                                   name="numero_documento"
                                   value="{{ old('numero_documento') }}"
                                   placeholder="Ej: F001-000123">
                            @error('numero_documento')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fecha_documento">Fecha del Documento</label>
                            <input type="date"
                                   class="form-control @error('fecha_documento') is-invalid @enderror"
                                   id="fecha_documento"
                                   name="fecha_documento"
                                   value="{{ old('fecha_documento') }}">
                            @error('fecha_documento')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="archivo">Archivo <span class="text-danger">*</span></label>
                    <div class="custom-file">
                        <input type="file"
                               class="custom-file-input @error('archivo') is-invalid @enderror"
                               id="archivo"
                               name="archivo"
                               required
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                        <label class="custom-file-label" for="archivo">Seleccionar archivo (máx. 10MB)</label>
                    </div>
                    @error('archivo')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    <small class="form-text text-muted">Formatos permitidos: PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG</small>
                </div>

                <div class="form-group">
                    <label for="observaciones">Observaciones</label>
                    <textarea class="form-control @error('observaciones') is-invalid @enderror"
                              id="observaciones"
                              name="observaciones"
                              rows="3"
                              placeholder="Notas adicionales sobre el documento...">{{ old('observaciones') }}</textarea>
                    @error('observaciones')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Documento
                    </button>
                    <a href="{{ route('admin.documentos-clientes.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script>
        // Mostrar nombre del archivo seleccionado
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).siblings('.custom-file-label').addClass('selected').html(fileName);
        });

        // Cargar cotizaciones cuando se selecciona un cliente
        function cargarCotizaciones(clienteId) {
            if (!clienteId) {
                $('#cotizacion_id').html('<option value="">Seleccione una cotización</option>');
                return;
            }

            $.ajax({
                url: '{{ route("api.cotizaciones.cliente", ":id") }}'.replace(':id', clienteId),
                method: 'GET',
                success: function(cotizaciones) {
                    let options = '<option value="">Seleccione una cotización</option>';
                    cotizaciones.forEach(function(cotizacion) {
                        const fecha = new Date(cotizacion.fecha_emision).toLocaleDateString('es-ES');
                        options += `<option value="${cotizacion.id}">${cotizacion.numero_cotizacion} - ${fecha}</option>`;
                    });
                    $('#cotizacion_id').html(options);
                },
                error: function() {
                    $('#cotizacion_id').html('<option value="">Error al cargar cotizaciones</option>');
                }
            });
        }
    </script>
@stop

