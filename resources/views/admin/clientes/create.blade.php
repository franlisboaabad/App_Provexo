@extends('adminlte::page')

@section('title', 'Nuevo Cliente')
{{-- @section('plugins.Sweetalert2', true) --}}
@section('content_header')
    <h1>Registro de cliente</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6 col-xs-4">
            <div class="card">

                <div class="card-body">

                    {{-- @include('validators.forms') --}}

                    <div id="map"></div>


                    <form action="" method="POST" id="form_cliente">
                        <div class="form group pb-3">
                            <label for="">Nombre</label>
                            <input type="text" placeholder="Ingrese Nombre" name="nombre" id="nombre"
                                class="form-control" required value="{{ old('nombre') }}">
                        </div>
                        <div class="form group pb-3">
                            <label for="">Apellidos</label>
                            <input type="text" placeholder="Ingrese Apellidos" name="apellidos" id="apellidos"
                                class="form-control" required value="{{ old('apellidos') }}">
                        </div>
                        <div class="form group pb-3">
                            <div class="row">
                                <div class="col">
                                    <label for="">DNI</label>
                                    <input type="text" placeholder="DNI" name="dni" id="dni"
                                        class="form-control" required value="{{ old('dni') }}" maxlength="8">
                                </div>
                                <div class="col">
                                    <label for="">Celular</label>
                                    <input type="text" placeholder="Celular" name="celular" id="celular"
                                        class="form-control" required value="{{ old('celular') }}" maxlength="9">
                                </div>
                            </div>
                        </div>
                        <div class="form group pb-3">
                            <label for="">E-mail</label>
                            <input type="email" placeholder="E-mail" name="email" id="email" class="form-control"
                                required value="{{ old('email') }}">
                        </div>
                        <div class="form group pb-3">
                            <label for="">Tienda</label>
                            <input type="text" placeholder="Ingrese nombre de Tienda" name="nombre_tienda" id="nombre"
                                class="form-control" required value="{{ old('nombre_tienda') }}">
                        </div>

                        <div class="form group pb-3">
                            <label for="">Dirección</label>
                            <input type="text" placeholder="Ingrese dirección" name="direccion" id="direccion"
                                class="form-control" required value="{{ old('direccion') }}">
                        </div>



                        <div class="form group pb-3">
                            <label for="">Latitud</label>
                            <input type="text" placeholder="Latitud" name="latitud" id="latitude" class="form-control"
                                value="{{ old('latitud') }}">
                        </div>

                        <div class="form group pb-3">
                            <label for="">Longitud</label>
                            <input type="text" placeholder="Longitud" name="longitud" id="longitude" class="form-control"
                                value="{{ old('longitud') }}">
                        </div>



                        <div class="form group pb-3">
                            <label for="">Ubicacion google maps</label>
                            <input type="text" placeholder="Ingrese dirección" name="geolocalizacion"
                                id="geolocalizacion" class="form-control" value="{{ old('geolocalizacion') }}">
                        </div>


                        <div class="form group mt-3">
                            @csrf
                            <button type="button" id="btn_Register" class="btn btn-success btn-xs"
                                data-url="{{ route('clientes.store') }}">Registrar cliente</button>
                            <a href="{{ route('clientes.index') }}" class="btn btn-warning btn-xs">Lista de clientes</a>
                        </div>
                    </form>

                    <p id="mapUrl"></p>


                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
@stop

@section('js')
    {{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/cliente.js') }}"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {

            //variables
            //funciones

            function getUbicación() {
                //   Solicitar permiso para acceder a la ubicación del usuario
                navigator.geolocation.getCurrentPosition(function(position) {
                    const latitud = position.coords.latitude;
                    const longitud = position.coords.longitude;

                    // Ahora puedes enviar estas coordenadas al servidor o guardarlas en tu base de datos.
                    // Por ejemplo:
                    // - Enviar a una API de backend.
                    // - Guardar en una tabla de ubicaciones.

                    console.log("Ubicación actual:");
                    console.log("Latitud:", latitud);
                    console.log("Longitud:", longitud);

                    // Construye la URL de Google Maps
                    const geo = `https://www.google.com/maps?q=${latitud},${longitud}`;

                    console.log(geo);

                    $('#latitude').val(latitud);
                    $('#longitude').val(longitud);
                    $('#geolocalizacion').val(geo);

                });

                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(showPosition, showError);
                } else {
                    alert("La geolocalización no es soportada por este navegador.");
                }
            }


            // llamar funciones

            getUbicación();

        });
    </script>

@stop
