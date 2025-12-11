@extends('adminlte::page')

@section('title', 'Lista de usuarios')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <p>Lista de usuarios</p>
    <div class="card">
        <div class="card-body">
            <a href="">Nuevo Usuario</a>
            <hr>

            <table class="table" id="table-usuarios">
                <thead>
                    <th>#</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Opciones</th>
                </thead>
                <tbody>
                    @foreach ($usuarios as $usuario)
                        <tr>
                            <td>{{ $usuario->id }}</td>
                            <td>{{ $usuario->name }}</td>
                            <td>{{ $usuario->email }}</td>
                            <td>
                                <form action="{{ route('usuarios.destroy', $usuario ) }}" method="POST">

                                    <a href="{{ route('usuarios.edit', $usuario ) }}" class="btn btn-info btn-xs">Editar</a>

                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('¿Esta seguro de eliminar el registro?')">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log('Hi!'); </script>
@stop