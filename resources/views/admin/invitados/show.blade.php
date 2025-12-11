@extends('adminlte::page')

@section('title', 'Invitado')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <p>Welcome to this beautiful admin panel.</p>
    <div class="card">
        <div class="card-body">
            <h1>DATOS DEL INVITADO</h1>
            <hr>

            <h3>Nombres y apellidos: {{ $invitado->nombre }} {{ $invitado->apellidos }}</h3>
            <h3>DNI {{ $invitado->dni }} </h3>
            <h3>Celular {{ $invitado->celular }} </h3>
            <h3>Dirección{{ $invitado->direccion }} </h3>
            <h3>E-mail {{ $invitado->email }} </h3>

            {!! QrCode::size(200)->generate($invitado->dni) !!}

        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log('Hi!'); </script>
@stop