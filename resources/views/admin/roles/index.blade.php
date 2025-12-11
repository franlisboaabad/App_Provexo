@extends('adminlte::page')

@section('title', 'Lista de roles')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <p>Welcome to this beautiful admin panel.</p>
    <div class="card">
        <div class="card-body">
            <a href="{{ route('roles.create') }}">Nuevo Rol</a> <hr>
            <table class="table">
                <thead>
                    <th>#</th>
                    <th>Role</th>
                    <th>Opciones</th>
                </thead>
                <tbody>
                    @foreach ($roles as $role)
                        <tr>
                            <td>{{ $role->id }}</td>
                            <td>{{ $role->name }}</td>
                            <td>
                                <form action="{{ route('roles.destroy', $role) }}" method="POST">

                                    <a href="{{ route('roles.edit', $role ) }}" class="btn btn-info btn-xs">Editar</a>
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-xs" onclick="return confirm('¿Esta seguro de eliminar el registro?')">Eliminar</button>
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