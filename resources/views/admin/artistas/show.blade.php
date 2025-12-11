@extends('adminlte::page')
@section('title', 'Ver Artista')
@section('content_header')
    <h1>Artista</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">

                    <img src="{{ $artista->get_imagen }}" alt="" class="img-fluid">
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h2>{{ $artista->nombre }}</h2>
                    <hr>
                    <p>{{ $artista->descripcion }}</p>
                </div>
            </div>
        </div>

    </div>
@stop

@section('css')

@stop

