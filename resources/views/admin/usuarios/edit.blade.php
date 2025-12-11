@extends('adminlte::page')

@section('title', 'Editar Usuario')

@section('content_header')
    <h1>Editar Usuario</h1>
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
        <div class="card-header">
            <h5>Usuario: {{ $usuario->name }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.usuarios.update', $usuario) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Nombre Completo</label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name"
                                   name="name"
                                   value="{{ old('name', $usuario->name) }}"
                                   readonly>
                            <small class="form-text text-muted">El nombre se actualiza desde el registro</small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   id="email"
                                   name="email"
                                   value="{{ old('email', $usuario->email) }}"
                                   readonly>
                            <small class="form-text text-muted">El email se actualiza desde el registro</small>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="form-group">
                    <label for="roles">Asignar Roles al Usuario <span class="text-danger">*</span></label>
                    <div class="border p-3 rounded" style="max-height: 300px; overflow-y: auto;">
                        @forelse ($roles as $rol)
                            <div class="form-check mb-2">
                                <input class="form-check-input"
                                       type="checkbox"
                                       value="{{ $rol->id }}"
                                       id="role_{{ $rol->id }}"
                                       name="roles[]"
                                       {{ $usuario->roles->pluck('id')->contains($rol->id) ? 'checked' : '' }}>
                                <label class="form-check-label" for="role_{{ $rol->id }}">
                                    <strong>{{ $rol->name }}</strong>
                                    @if($rol->permissions->count() > 0)
                                        <small class="text-muted d-block">
                                            Permisos: {{ $rol->permissions->pluck('name')->implode(', ') }}
                                        </small>
                                    @endif
                                </label>
                            </div>
                        @empty
                            <p class="text-muted">No hay roles disponibles</p>
                        @endforelse
                    </div>
                    @error('roles')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    <small class="form-text text-muted">Selecciona uno o más roles para este usuario</small>
                </div>

                <hr>

                <div class="form-group">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Actualizar Roles del Usuario
                    </button>
                    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary">
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
        console.log('Formulario de edición de usuario cargado');
    </script>
@stop
