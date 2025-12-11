@extends('adminlte::page')

@section('title', 'Editar usuario')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <p>Welcome to this beautiful admin panel.</p>
    <div class="card">


        @if (session('success'))
            <div id="success-message" class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="card-body">
            <form action="{{ route('usuarios.update', $usuario) }}" method="POST">


                <div class="form-group">
                    <label for="">Nombre</label>
                    <input type="text" class="form-control" name="name" value="{{ old('name', $usuario->name) }}">
                </div>

                <div class="form-group">
                    <label for="">Email</label>
                    <input type="text" class="form-control" name="name" value="{{ old('name', $usuario->email) }}">
                </div>

                <div class="form-group">
                    <label for="">Passsword</label>
                    <input type="text" class="form-control" name="password" value="{{ old('password') }}">
                </div>

                <hr>


                <div class="form-group">
                    <label for="">Rol de usuario</label>

                    @foreach ($roles as $rol)
                        <br>
                        <input type="checkbox" value="{{ $rol->id }}" name="roles[]"
                            {{ $usuario->roles->pluck('id')->contains($rol->id) ? 'checked' : '' }}>
                        {{ $rol->name }}
                    @endforeach
                </div>


                @csrf
                @method('PUT')
                <div class="form-group">
                    <button class="btn btn-success btn-xs">Editar usuario</button>
                    <a href="{{ route('usuarios.index') }}" class="btn btn-danger btn-xs">Cancelar</a>
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
        console.log('Hi!');
    </script>
@stop
