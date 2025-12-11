@extends('adminlte::page')

@section('title', 'Editar rol')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <p>Welcome to this beautiful admin panel.</p>
    <div class="card">

        @if (session()->has('success'))
            <div class="alert alert-success">
                {{ session()->get('success') }}
            </div>
        @endif

        <div class="card-body">
            <form action="{{ route('roles.update', $role) }}" method="POST">
                <div class="form-group">
                    <label for="">Rol</label>
                    <input type="text" name="name" value="{{ old('name', $role->name) }}" class="form-control">
                </div>

                <hr>
                <p>Lista de permisos</p>
                @foreach ($permisos as $permiso)
                    <br>
                    <input type="checkbox" value="{{ $permiso->id }}" name="permisos[]"
                        {{ $role->hasPermissionTo($permiso->name) ? 'checked' : '' }}> {{ $permiso->description }}
                @endforeach

                <div class="form-group mt-2">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-success btn-xs">Editar Rol</button>
                    <a href="{{ route('roles.index') }}" class="btn btn-danger btn-xs">Cancelar</a>
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
