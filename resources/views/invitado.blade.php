@extends('layouts.app')
@section('contenido')
    <div class="container">



        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif



        <div class="card">
            <div class="card-body">
                <p>Nombres: {{ $invitado->nombre }}</p>
                <p>Apellidos: {{ $invitado->apellidos }}</p>
                <p>Dni: {{ $invitado->dni }}</p>
                <p>E-mail: {{ $invitado->email }}</p>
                <p>Celular: {{ $invitado->celular }}</p>
                <p>Dirección: {{ $invitado->direccion }}</p>
                <a href="{{ route('invitados.registrar', $invitado->id) }}" class="btn btn-succes btn-xs">Registrar
                    Invitado</a>
            </div>
        </div>
    </div>
@endsection
