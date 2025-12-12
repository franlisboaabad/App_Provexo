@extends('adminlte::page')

@section('title', 'Registrar Empresa')

@section('content_header')
    <h1>Registrar Nueva Empresa</h1>
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

    <form action="{{ route('admin.empresas.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Información General -->
        <div class="card">
            <div class="card-header">
                <h5>Información General</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="razon_social">Razón Social <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('razon_social') is-invalid @enderror"
                                   id="razon_social"
                                   name="razon_social"
                                   value="{{ old('razon_social') }}"
                                   required
                                   autofocus>
                            @error('razon_social')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombre_comercial">Nombre Comercial</label>
                            <input type="text"
                                   class="form-control @error('nombre_comercial') is-invalid @enderror"
                                   id="nombre_comercial"
                                   name="nombre_comercial"
                                   value="{{ old('nombre_comercial') }}">
                            @error('nombre_comercial')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="ruc">RUC <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('ruc') is-invalid @enderror"
                                   id="ruc"
                                   name="ruc"
                                   value="{{ old('ruc') }}"
                                   maxlength="20"
                                   required>
                            @error('ruc')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="tipo_empresa">Tipo de Empresa</label>
                            <select class="form-control @error('tipo_empresa') is-invalid @enderror"
                                    id="tipo_empresa"
                                    name="tipo_empresa">
                                <option value="">Seleccione...</option>
                                <option value="EIRL" {{ old('tipo_empresa') == 'EIRL' ? 'selected' : '' }}>EIRL</option>
                                <option value="SAC" {{ old('tipo_empresa') == 'SAC' ? 'selected' : '' }}>SAC</option>
                                <option value="SRL" {{ old('tipo_empresa') == 'SRL' ? 'selected' : '' }}>SRL</option>
                                <option value="SA" {{ old('tipo_empresa') == 'SA' ? 'selected' : '' }}>SA</option>
                                <option value="SAA" {{ old('tipo_empresa') == 'SAA' ? 'selected' : '' }}>SAA</option>
                            </select>
                            @error('tipo_empresa')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="representante_legal">Representante Legal</label>
                            <input type="text"
                                   class="form-control @error('representante_legal') is-invalid @enderror"
                                   id="representante_legal"
                                   name="representante_legal"
                                   value="{{ old('representante_legal') }}">
                            @error('representante_legal')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea class="form-control @error('descripcion') is-invalid @enderror"
                              id="descripcion"
                              name="descripcion"
                              rows="3">{{ old('descripcion') }}</textarea>
                    @error('descripcion')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Ubicación y Contacto -->
        <div class="card mt-3">
            <div class="card-header">
                <h5>Ubicación y Contacto</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="direccion">Dirección</label>
                            <input type="text"
                                   class="form-control @error('direccion') is-invalid @enderror"
                                   id="direccion"
                                   name="direccion"
                                   value="{{ old('direccion') }}">
                            @error('direccion')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="distrito">Distrito</label>
                            <input type="text"
                                   class="form-control @error('distrito') is-invalid @enderror"
                                   id="distrito"
                                   name="distrito"
                                   value="{{ old('distrito') }}">
                            @error('distrito')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="ciudad">Ciudad</label>
                            <input type="text"
                                   class="form-control @error('ciudad') is-invalid @enderror"
                                   id="ciudad"
                                   name="ciudad"
                                   value="{{ old('ciudad') }}">
                            @error('ciudad')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="provincia">Provincia</label>
                            <input type="text"
                                   class="form-control @error('provincia') is-invalid @enderror"
                                   id="provincia"
                                   name="provincia"
                                   value="{{ old('provincia') }}">
                            @error('provincia')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="telefono">Teléfono</label>
                            <input type="text"
                                   class="form-control @error('telefono') is-invalid @enderror"
                                   id="telefono"
                                   name="telefono"
                                   value="{{ old('telefono') }}"
                                   maxlength="20">
                            @error('telefono')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="celular">Celular</label>
                            <input type="text"
                                   class="form-control @error('celular') is-invalid @enderror"
                                   id="celular"
                                   name="celular"
                                   value="{{ old('celular') }}"
                                   maxlength="20">
                            @error('celular')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   id="email"
                                   name="email"
                                   value="{{ old('email') }}">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="web">Sitio Web</label>
                    <input type="url"
                           class="form-control @error('web') is-invalid @enderror"
                           id="web"
                           name="web"
                           value="{{ old('web') }}"
                           placeholder="https://www.ejemplo.com">
                    @error('web')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Logo y Configuración -->
        <div class="card mt-3">
            <div class="card-header">
                <h5>Logo y Configuración</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="logo">Logo de la Empresa</label>
                            <div class="custom-file">
                                <input type="file"
                                       class="custom-file-input @error('logo') is-invalid @enderror"
                                       id="logo"
                                       name="logo"
                                       accept="image/jpeg,image/png,image/jpg,image/gif">
                                <label class="custom-file-label" for="logo">Seleccionar archivo...</label>
                            </div>
                            @error('logo')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <small class="form-text text-muted">Formatos: JPEG, PNG, JPG, GIF. Tamaño máximo: 2MB</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="checkbox"
                                       id="activo"
                                       name="activo"
                                       value="1"
                                       {{ old('activo', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="activo">
                                    Empresa Activa
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="checkbox"
                                       id="es_principal"
                                       name="es_principal"
                                       value="1"
                                       {{ old('es_principal') ? 'checked' : '' }}>
                                <label class="form-check-label" for="es_principal">
                                    Marcar como Empresa Principal
                                </label>
                            </div>
                            <small class="form-text text-muted">La empresa principal se usará para generar cotizaciones por defecto</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones -->
        <div class="card mt-3">
            <div class="card-body">
                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-save"></i> Registrar Empresa
                    </button>
                    <a href="{{ route('admin.empresas.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </div>
        </div>
    </form>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        // Mostrar nombre del archivo seleccionado
        document.getElementById('logo').addEventListener('change', function(e) {
            var fileName = e.target.files[0]?.name || 'Seleccionar archivo...';
            var nextSibling = e.target.nextElementSibling;
            nextSibling.innerText = fileName;
        });
    </script>
@stop

