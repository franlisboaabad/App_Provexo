@extends('adminlte::page')

@section('title', 'Cliente')

@section('content_header')
    <h1>Ver cliente</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead>
                    <th>#</th>
                    <th>Nombre y Apellidos</th>
                    <th>DNI</th>
                    <th>Celular</th>
                    <th>Email</th>
                    <th>Tienda</th>
                    <th>Dirección</th>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $cliente->id }}</td>
                        <td>{{ $cliente->nombre }} {{ $cliente->apellidos }}</td>
                        <td>{{ $cliente->dni }}</td>
                        <td>{{ $cliente->celular }}</td>
                        <td>{{ $cliente->email }}</td>
                        <td>{{ $cliente->nombre_tienda }}</td>
                        <td>{{ $cliente->direccion }}</td>
                    </tr>
                </tbody>
            </table>
            <hr>
            <div class="mt-5 mb-5">
                <p>Latitude: {{ $cliente->latitud }} </p>
                <p>Longitude: {{ $cliente->longitud }} </p>
                <iframe
                width="600"
                height="450"
                frameborder="0"
                style="border:0"
                src="https://www.google.com/maps?q={{$cliente->latitud}},{{$cliente->longitud}}&output=embed"
                allowfullscreen
              ></iframe>
            </div>
        </div>
    </div>
@stop

@section('css')

@stop

@section('js')

@stop
