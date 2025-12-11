@extends('adminlte::page')

@section('title', 'Equipo registrado')
@section('plugins.Datatables', true)
@section('content_header')
    <h1>Equipo</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <a href="{{ route('equipos.index') }}" class="btn btn-xs btn-warning">Lista de equipos</a>
            <hr>
            <h1 class="font-weight-bold">Cliente: {{ $equipo->cliente->nombre }} {{ $equipo->cliente->apellidos }} </h1>


            <table class="table mt-5">
                <thead>
                    <th>Tipo de equipo</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Numero de serie</th>
                    <th>Estado</th>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $equipo->tipo->tipo }}</td>
                        <td>{{ $equipo->marca }}</td>
                        <td>{{ $equipo->modelo }}</td>
                        <td>{{ $equipo->numero_de_serie }}</td>
                        <td>{{ $equipo->estado_reparacion }}</td>
                    </tr>
                </tbody>
            </table>


            <div class="mt-5">
                <h3 class="font-weight-bold">Observaciones</h3>
                <p>Caracteristicas del Equipo: {{ $equipo->caracteristicas }}</p> <hr>
                <p>Problemas del equipo: {{ $equipo->problema_falla }}</p> <hr>
                <p>Accesorios adicionales: {{ $equipo->accesorios_adicionales }}</p> <hr>
            </div>

            <div>
                <h3 class="font-weight-bold">Imagenes de Equipo</h3> <br>
                @foreach ($equipo->imagenes as $imagen)
                    <img src="{{ asset('storage/' . $imagen->file) }}" alt="Imagen del Equipo" width="250px">
                @endforeach


            </div>

        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('#table-equipos').DataTable();
        });
    </script>
@stop
