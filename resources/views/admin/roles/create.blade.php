@extends('adminlte::page')

@section('title', 'Nuevo rol')

@section('content_header')

    <div class="row">
        <div class="col-md">
            <h1>Nuevo Rol</h1>
        </div>
    </div>

@endsection

@section('content')

    <div class="card">
        <div class="card-body">

            @if ($errors->any())
                <ul>
                    @foreach ($errors->all() as $error)
                        <li class="text-red">{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

            @if (session()->has('success'))
                <div class="alert alert-success">
                    {{ session()->get('success') }}
                </div>
            @endif


            <div class="row">
                <div class="col-md-6">
                    <form action="{{ route('roles.store') }}" method="POST">
                        <div class="form-group">
                            <label for="">Name</label>
                            <input type="text" name="name" placeholder="Ingrese Rol" class="form-control">
                        </div>

                        <hr>
                        <h5>Lista de permisos</h5>
                            @foreach ($permisos as $permiso)
                            <input type="checkbox" value="{{ $permiso->id }}" name="permisos[]"> {{ $permiso->description }} <br>
                            @endforeach
                        <hr>

                        <div class="form-group">
                            @csrf

                            <button type="submit" class="btn btn-success btn-xs">Agregar rol</button>
                            <a href="{{ route('roles.index') }}" class="btn btn-danger btn-xs">Lista de Roles</a>
                        </div>
                    </form>
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
        console.log('hi!')
    </script>
@stop
