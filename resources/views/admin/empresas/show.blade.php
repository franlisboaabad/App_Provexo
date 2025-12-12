@extends('adminlte::page')

@section('title', 'Detalle de la Empresa')

@section('content_header')
    <h1>Detalle de la Empresa: {{ $empresa->razon_social }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="card-tools">
                @can('admin.empresas.edit')
                    <a href="{{ route('admin.empresas.edit', $empresa) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                @endcan
                <a href="{{ route('admin.empresas.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Información General -->
                <div class="col-md-6">
                    <h5>Información General</h5>
                    <hr>
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">ID:</th>
                            <td>{{ $empresa->id }}</td>
                        </tr>
                        <tr>
                            <th>Razón Social:</th>
                            <td><strong>{{ $empresa->razon_social }}</strong></td>
                        </tr>
                        <tr>
                            <th>Nombre Comercial:</th>
                            <td>{{ $empresa->nombre_comercial ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>RUC:</th>
                            <td><strong>{{ $empresa->ruc }}</strong></td>
                        </tr>
                        <tr>
                            <th>Tipo de Empresa:</th>
                            <td>{{ $empresa->tipo_empresa ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Representante Legal:</th>
                            <td>{{ $empresa->representante_legal ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Estado:</th>
                            <td>
                                @if($empresa->activo)
                                    <span class="badge badge-success">Activa</span>
                                @else
                                    <span class="badge badge-danger">Inactiva</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Empresa Principal:</th>
                            <td>
                                @if($empresa->es_principal)
                                    <span class="badge badge-primary"><i class="fas fa-star"></i> Principal</span>
                                @else
                                    <span class="text-muted">No</span>
                                @endif
                            </td>
                        </tr>
                        @if($empresa->descripcion)
                        <tr>
                            <th>Descripción:</th>
                            <td>{{ $empresa->descripcion }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Logo:</th>
                            <td>
                                @if($empresa->logo)
                                    <img src="{{ Storage::url($empresa->logo) }}"
                                         alt="Logo de {{ $empresa->razon_social }}"
                                         class="img-thumbnail"
                                         style="max-width: 150px; max-height: 150px;">
                                @else
                                    <span class="text-muted">No hay logo</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Ubicación y Contacto -->
                <div class="col-md-6">
                    <h5>Ubicación y Contacto</h5>
                    <hr>
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">Dirección:</th>
                            <td>{{ $empresa->direccion ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Distrito:</th>
                            <td>{{ $empresa->distrito ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Ciudad:</th>
                            <td>{{ $empresa->ciudad ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Provincia:</th>
                            <td>{{ $empresa->provincia ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Dirección Completa:</th>
                            <td>{{ $empresa->direccion_completa ?: 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Teléfono:</th>
                            <td>{{ $empresa->telefono ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Celular:</th>
                            <td>{{ $empresa->celular ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $empresa->email ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Sitio Web:</th>
                            <td>
                                @if($empresa->web)
                                    <a href="{{ $empresa->web }}" target="_blank">{{ $empresa->web }}</a>
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Creado:</th>
                            <td>{{ $empresa->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Actualizado:</th>
                            <td>{{ $empresa->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Series de Cotización -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Series de Cotización</h5>
                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modalNuevaSerie">
                                <i class="fas fa-plus"></i> Nueva Serie
                            </button>
                        </div>
                        <div class="card-body">
                            @if($empresa->seriesCotizacion->count() > 0)
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>Serie</th>
                                            <th>Descripción</th>
                                            <th>Estado</th>
                                            <th>Principal</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($empresa->seriesCotizacion as $serie)
                                            <tr>
                                                <td><strong>{{ $serie->serie }}</strong></td>
                                                <td>{{ $serie->descripcion ?? '-' }}</td>
                                                <td>
                                                    @if($serie->activa)
                                                        <span class="badge badge-success">Activa</span>
                                                    @else
                                                        <span class="badge badge-danger">Inactiva</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($serie->es_principal)
                                                        <span class="badge badge-primary"><i class="fas fa-star"></i> Principal</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-warning" onclick="editarSerie({{ $serie->id }}, '{{ $serie->serie }}', '{{ $serie->descripcion ?? '' }}', {{ $serie->activa ? 'true' : 'false' }}, {{ $serie->es_principal ? 'true' : 'false' }})">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <form action="{{ route('admin.series.destroy', $serie) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('¿Está seguro de eliminar esta serie?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-muted mb-0">No hay series de cotización registradas.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cuentas Bancarias -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Cuentas Bancarias</h5>
                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modalNuevaCuenta">
                                <i class="fas fa-plus"></i> Nueva Cuenta
                            </button>
                        </div>
                        <div class="card-body">
                            @if($empresa->cuentasBancarias->count() > 0)
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>Banco</th>
                                            <th>Tipo</th>
                                            <th>Número de Cuenta</th>
                                            <th>CCI</th>
                                            <th>Moneda</th>
                                            <th>Estado</th>
                                            <th>Principal</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($empresa->cuentasBancarias as $cuenta)
                                            <tr>
                                                <td><strong>{{ $cuenta->banco }}</strong></td>
                                                <td>{{ $cuenta->tipo_cuenta ?? '-' }}</td>
                                                <td><strong>{{ $cuenta->numero_cuenta }}</strong></td>
                                                <td>{{ $cuenta->cci_formateado ?? '-' }}</td>
                                                <td>
                                                    @if($cuenta->moneda_cuenta == 'PEN')
                                                        <span class="badge badge-info">PEN</span>
                                                    @elseif($cuenta->moneda_cuenta == 'USD')
                                                        <span class="badge badge-success">USD</span>
                                                    @else
                                                        {{ $cuenta->moneda_cuenta }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($cuenta->activa)
                                                        <span class="badge badge-success">Activa</span>
                                                    @else
                                                        <span class="badge badge-danger">Inactiva</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($cuenta->es_principal)
                                                        <span class="badge badge-primary"><i class="fas fa-star"></i> Principal</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-warning" onclick="editarCuenta({{ $cuenta->id }}, '{{ $cuenta->banco }}', '{{ $cuenta->tipo_cuenta ?? '' }}', '{{ $cuenta->numero_cuenta }}', '{{ $cuenta->numero_cuenta_interbancario ?? '' }}', '{{ $cuenta->moneda_cuenta }}', {{ $cuenta->activa ? 'true' : 'false' }}, {{ $cuenta->es_principal ? 'true' : 'false' }})">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <form action="{{ route('admin.cuentas.destroy', $cuenta) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('¿Está seguro de eliminar esta cuenta bancaria?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-muted mb-0">No hay cuentas bancarias registradas.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Nueva Serie -->
    <div class="modal fade" id="modalNuevaSerie" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nueva Serie de Cotización</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.series.store', $empresa) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="serie">Serie <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="serie" name="serie" required maxlength="10" placeholder="COT">
                            <small class="form-text text-muted">Ejemplo: COT, FACT, etc.</small>
                        </div>
                        <div class="form-group">
                            <label for="descripcion_serie">Descripción</label>
                            <input type="text" class="form-control" id="descripcion_serie" name="descripcion" maxlength="255">
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="activa_serie" name="activa" value="1" checked>
                            <label class="form-check-label" for="activa_serie">Serie Activa</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="es_principal_serie" name="es_principal" value="1">
                            <label class="form-check-label" for="es_principal_serie">Marcar como Principal</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Serie -->
    <div class="modal fade" id="modalEditarSerie" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Serie de Cotización</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="formEditarSerie" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="serie_edit">Serie <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="serie_edit" name="serie" required maxlength="10">
                        </div>
                        <div class="form-group">
                            <label for="descripcion_serie_edit">Descripción</label>
                            <input type="text" class="form-control" id="descripcion_serie_edit" name="descripcion" maxlength="255">
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="activa_serie_edit" name="activa" value="1">
                            <label class="form-check-label" for="activa_serie_edit">Serie Activa</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="es_principal_serie_edit" name="es_principal" value="1">
                            <label class="form-check-label" for="es_principal_serie_edit">Marcar como Principal</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Nueva Cuenta -->
    <div class="modal fade" id="modalNuevaCuenta" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nueva Cuenta Bancaria</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.cuentas.store', $empresa) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="banco">Banco <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="banco" name="banco" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tipo_cuenta">Tipo de Cuenta</label>
                                    <select class="form-control" id="tipo_cuenta" name="tipo_cuenta">
                                        <option value="">Seleccione...</option>
                                        <option value="Ahorros">Ahorros</option>
                                        <option value="Corriente">Corriente</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="moneda_cuenta">Moneda</label>
                                    <select class="form-control" id="moneda_cuenta" name="moneda_cuenta">
                                        <option value="PEN">PEN (Soles)</option>
                                        <option value="USD">USD (Dólares)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="numero_cuenta">Número de Cuenta <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="numero_cuenta" name="numero_cuenta" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="numero_cuenta_interbancario">CCI</label>
                                    <input type="text" class="form-control" id="numero_cuenta_interbancario" name="numero_cuenta_interbancario">
                                </div>
                            </div>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="activa_cuenta" name="activa" value="1" checked>
                            <label class="form-check-label" for="activa_cuenta">Cuenta Activa</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="es_principal_cuenta" name="es_principal" value="1">
                            <label class="form-check-label" for="es_principal_cuenta">Marcar como Principal</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Cuenta -->
    <div class="modal fade" id="modalEditarCuenta" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Cuenta Bancaria</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="formEditarCuenta" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="banco_edit">Banco <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="banco_edit" name="banco" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tipo_cuenta_edit">Tipo de Cuenta</label>
                                    <select class="form-control" id="tipo_cuenta_edit" name="tipo_cuenta">
                                        <option value="">Seleccione...</option>
                                        <option value="Ahorros">Ahorros</option>
                                        <option value="Corriente">Corriente</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="moneda_cuenta_edit">Moneda</label>
                                    <select class="form-control" id="moneda_cuenta_edit" name="moneda_cuenta">
                                        <option value="PEN">PEN (Soles)</option>
                                        <option value="USD">USD (Dólares)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="numero_cuenta_edit">Número de Cuenta <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="numero_cuenta_edit" name="numero_cuenta" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="numero_cuenta_interbancario_edit">CCI</label>
                                    <input type="text" class="form-control" id="numero_cuenta_interbancario_edit" name="numero_cuenta_interbancario">
                                </div>
                            </div>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="activa_cuenta_edit" name="activa" value="1">
                            <label class="form-check-label" for="activa_cuenta_edit">Cuenta Activa</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="es_principal_cuenta_edit" name="es_principal" value="1">
                            <label class="form-check-label" for="es_principal_cuenta_edit">Marcar como Principal</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        // Funciones para editar serie
        function editarSerie(id, serie, descripcion, activa, es_principal) {
            $('#formEditarSerie').attr('action', '{{ url("series") }}/' + id);
            $('#serie_edit').val(serie);
            $('#descripcion_serie_edit').val(descripcion);
            $('#activa_serie_edit').prop('checked', activa);
            $('#es_principal_serie_edit').prop('checked', es_principal);
            $('#modalEditarSerie').modal('show');
        }

        // Funciones para editar cuenta
        function editarCuenta(id, banco, tipo_cuenta, numero_cuenta, cci, moneda, activa, es_principal) {
            $('#formEditarCuenta').attr('action', '{{ url("cuentas") }}/' + id);
            $('#banco_edit').val(banco);
            $('#tipo_cuenta_edit').val(tipo_cuenta);
            $('#numero_cuenta_edit').val(numero_cuenta);
            $('#numero_cuenta_interbancario_edit').val(cci);
            $('#moneda_cuenta_edit').val(moneda);
            $('#activa_cuenta_edit').prop('checked', activa);
            $('#es_principal_cuenta_edit').prop('checked', es_principal);
            $('#modalEditarCuenta').modal('show');
        }

        // Limpiar modales al cerrarse
        $('#modalNuevaSerie, #modalNuevaCuenta').on('hidden.bs.modal', function () {
            $(this).find('form')[0].reset();
        });
    </script>
@stop

