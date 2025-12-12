@extends('adminlte::page')

@section('title', 'Editar Proveedor')

@section('content_header')
    <h1>Editar Proveedor</h1>
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
            <form action="{{ route('admin.proveedores.update', $proveedore) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombre">Nombre Completo <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('nombre') is-invalid @enderror"
                                   id="nombre"
                                   name="nombre"
                                   value="{{ old('nombre', $proveedore->nombre) }}"
                                   required
                                   autofocus>
                            @error('nombre')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   id="email"
                                   name="email"
                                   value="{{ old('email', $proveedore->email) }}">
                            @error('email')
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
                            <label for="celular">Celular</label>
                            <input type="text"
                                   class="form-control @error('celular') is-invalid @enderror"
                                   id="celular"
                                   name="celular"
                                   value="{{ old('celular', $proveedore->celular) }}"
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
                            <label for="empresa">Empresa</label>
                            <input type="text"
                                   class="form-control @error('empresa') is-invalid @enderror"
                                   id="empresa"
                                   name="empresa"
                                   value="{{ old('empresa', $proveedore->empresa) }}">
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
                                   value="{{ old('ruc', $proveedore->ruc) }}"
                                   maxlength="100">
                            @error('ruc')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="direccion">Dirección</label>
                            <textarea class="form-control @error('direccion') is-invalid @enderror"
                                      id="direccion"
                                      name="direccion"
                                      rows="3">{{ old('direccion', $proveedore->direccion) }}</textarea>
                            @error('direccion')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr>

                <div class="form-group">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Actualizar Proveedor
                    </button>
                    <a href="{{ route('admin.proveedores.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                    <a href="{{ route('admin.proveedores.show', $proveedore) }}" class="btn btn-info">
                        <i class="fas fa-eye"></i> Ver Detalles
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
        console.log('Formulario de edición de proveedor cargado');
    </script>
@stop

