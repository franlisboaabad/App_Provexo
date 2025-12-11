@extends('adminlte::page')

@section('title', 'Nuevo Rol')

@section('content_header')
    <h1>Registrar Nuevo Rol</h1>
@stop

@section('content')
    @if (session('success'))
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
        <div class="card-body">
            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="name">Nombre del Rol <span class="text-danger">*</span></label>
                    <input type="text"
                           class="form-control @error('name') is-invalid @enderror"
                           id="name"
                           name="name"
                           value="{{ old('name') }}"
                           placeholder="Ej: Editor, Moderador, etc."
                           required
                           autofocus>
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <hr>

                <div class="form-group">
                    <label for="permisos">Asignar Permisos</label>
                    <div class="border p-3 rounded" style="max-height: 400px; overflow-y: auto;">
                        @php
                            $permisosAgrupados = $permisos->groupBy(function($permiso) {
                                // Agrupar por el primer segmento del nombre (ej: admin.home -> admin)
                                $segmentos = explode('.', $permiso->name);
                                return $segmentos[0] ?? 'Otros';
                            });
                        @endphp

                        @foreach ($permisosAgrupados as $grupo => $permisosGrupo)
                            <div class="mb-4">
                                <h6 class="text-primary font-weight-bold">{{ strtoupper($grupo) }}</h6>
                                @foreach ($permisosGrupo as $permiso)
                                    <div class="form-check mb-2 ml-3">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               value="{{ $permiso->id }}"
                                               id="permiso_{{ $permiso->id }}"
                                               name="permisos[]">
                                        <label class="form-check-label" for="permiso_{{ $permiso->id }}">
                                            <strong>{{ $permiso->description ?? $permiso->name }}</strong>
                                            <small class="text-muted d-block">({{ $permiso->name }})</small>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                    <small class="form-text text-muted">Selecciona los permisos que tendrá este rol (opcional)</small>
                </div>

                <hr>

                <div class="form-group">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Crear Rol
                    </button>
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        console.log('Formulario de creación de rol cargado');
    </script>
@stop
