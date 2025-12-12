@extends('adminlte::page')

@section('title', 'Mi Perfil')

@section('content_header')
    <h1>Mi Perfil</h1>
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

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user"></i> Información Personal
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('patch')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Nombre Completo <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control @error('name') is-invalid @enderror"
                                           id="name"
                                           name="name"
                                           value="{{ old('name', $user->name) }}"
                                           required
                                           autofocus>
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Correo Electrónico <span class="text-danger">*</span></label>
                                    <input type="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           id="email"
                                           name="email"
                                           value="{{ old('email', $user->email) }}"
                                           required>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    @if (!$user->hasVerifiedEmail())
                                        <div class="alert alert-warning mt-2 mb-0">
                                            <small>
                                                <i class="fas fa-exclamation-triangle"></i> 
                                                <strong>Tu correo electrónico no está verificado.</strong> 
                                                Por favor, verifica tu correo para acceder a todas las funcionalidades.
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <hr>

                        <h5 class="mb-3">
                            <i class="fas fa-building"></i> Información de Cliente
                        </h5>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="celular">Celular</label>
                                    <input type="text"
                                           class="form-control @error('celular') is-invalid @enderror"
                                           id="celular"
                                           name="celular"
                                           value="{{ old('celular', $cliente->celular ?? '') }}"
                                           maxlength="20"
                                           placeholder="Ej: 987654321">
                                    @error('celular')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="empresa">Empresa</label>
                                    <input type="text"
                                           class="form-control @error('empresa') is-invalid @enderror"
                                           id="empresa"
                                           name="empresa"
                                           value="{{ old('empresa', $cliente->empresa ?? '') }}"
                                           placeholder="Nombre de la empresa">
                                    @error('empresa')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ruc">RUC</label>
                                    <input type="text"
                                           class="form-control @error('ruc') is-invalid @enderror"
                                           id="ruc"
                                           name="ruc"
                                           value="{{ old('ruc', $cliente->ruc ?? '') }}"
                                           maxlength="100"
                                           placeholder="Ej: 20123456789">
                                    @error('ruc')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-key"></i> Cambiar Contraseña
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.password.update') }}" method="POST">
                        @csrf
                        @method('patch')

                        <div class="form-group">
                            <label for="current_password">Contraseña Actual</label>
                            <input type="password"
                                   class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                                   id="current_password"
                                   name="current_password"
                                   required>
                            @error('current_password', 'updatePassword')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">Nueva Contraseña</label>
                            <input type="password"
                                   class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                                   id="password"
                                   name="password"
                                   required>
                            @error('password', 'updatePassword')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <small class="form-text text-muted">Mínimo 8 caracteres</small>
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Confirmar Nueva Contraseña</label>
                            <input type="password"
                                   class="form-control"
                                   id="password_confirmation"
                                   name="password_confirmation"
                                   required>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-key"></i> Cambiar Contraseña
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Información de Cuenta
                    </h3>
                </div>
                <div class="card-body">
                    <p><strong>Rol:</strong> 
                        @foreach($user->roles as $role)
                            <span class="badge badge-primary">{{ $role->name }}</span>
                        @endforeach
                    </p>
                    <p><strong>Estado:</strong> 
                        @if($user->activo)
                            <span class="badge badge-success">Activo</span>
                        @else
                            <span class="badge badge-danger">Inactivo</span>
                        @endif
                    </p>
                    <p><strong>Miembro desde:</strong> {{ $user->created_at->format('d/m/Y') }}</p>
                    @if($user->email_verified_at)
                        <p><strong>Email verificado:</strong> 
                            <span class="badge badge-success">
                                <i class="fas fa-check"></i> Verificado
                            </span>
                        </p>
                        <p class="text-muted small">
                            Verificado el: {{ $user->email_verified_at->format('d/m/Y H:i') }}
                        </p>
                    @else
                        <p><strong>Email verificado:</strong> 
                            <span class="badge badge-warning">
                                <i class="fas fa-exclamation-triangle"></i> No verificado
                            </span>
                        </p>
                        <form method="POST" action="{{ route('verification.send') }}" class="mt-2">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-info">
                                <i class="fas fa-envelope"></i> Reenviar Email de Verificación
                            </button>
                        </form>
                        @if (session('status') === 'verification-link-sent')
                            <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
                                <small>
                                    <i class="fas fa-check-circle"></i> Se ha enviado un nuevo enlace de verificación a tu correo electrónico.
                                </small>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
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
    <script>
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    </script>
@stop
