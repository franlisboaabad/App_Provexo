@extends('adminlte::page')

@section('title', 'Artistas')
@section('plugins.Datatables', true)
@section('content_header')
    <h1>Lista de Artistas</h1>
@stop

@section('content')

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="card">
        <div class="card-body">
            <a id="btnAbrirModal" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#ModalRegister">Nuevo
                Artista</a>
            <hr>

            <table class="table" id="table-artistas">
                <thead>
                    <th>#</th>
                    <th>Nombres</th>
                    <th>Breve descripcion</th>
                    <th>Estado</th>
                    <th>Config</th>
                </thead>
                <tbody>
                    @foreach ($artistas as $artista)
                        <tr>
                            <td>{{ $artista->id }}</td>
                            <td>{{ $artista->nombre }}</td>
                            <td>{{ $artista->descripcion }}</td>
                            <td>
                                @if ($artista)
                                    <span class="badge badge-success">Activo</span>
                                @else
                                    <span class="badge badge-success">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <form action="" method="POST">
                                    <a href="" class="btn btn-warning btn-xs btnAbrirModalEditar" data-toggle="modal"
                                        data-target="#ModalEditar" data-id="{{ $artista->id }}">Editar</a>
                                    <a href="{{ route('artistas.show', $artista) }}" class="btn btn-primary btn-xs">Ver</a>
                                    <button class="btn btn-danger btn-xs">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>


    <!-- Modal Add artista -->
    <div class="modal fade" id="ModalRegister" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Nuevo Artista</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form-registro-artista" method="POST" enctype="multipart/form-data">
                        <div class="group mb-3">
                            <label for="nombre">Nombre</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" required
                                placeholder="Ingrese Nombres y Apellidos">
                        </div>

                        <div class="group mb-3">
                            <label for="descripcion">Descripcion del Artista</label>
                            <textarea name="descripcion" id="descripcion" class="form-control" cols="30" rows="10"></textarea>
                        </div>

                        <div class="group mb-3">
                            <label for="imagen">Imagen del Artista</label>
                            <input type="file" id="imagen" name="imagen">
                        </div>


                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btn_Register"
                        data-url="{{ route('artistas.store') }}">Registrar Artista</button>

                </div>
            </div>
        </div>
    </div>

    {{-- Modal Update  --}}
    <div class="modal fade" id="ModalEditar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Editar Artista</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form-editar-artista" method="POST" enctype="multipart/form-data">

                        <div class="group mb-3">
                            <label for="nombre">Nombre</label>
                            <input type="hidden" name="id" id="artistaId">
                            <input type="text" name="nombre" id="nombreE" class="form-control" required
                                placeholder="Ingrese Nombres y Apellidos">
                        </div>

                        <div class="group mb-3">
                            <label for="descripcion">Descripcion del Artista</label>
                            <textarea name="descripcion" id="descripcionE" class="form-control" cols="30" rows="10"></textarea>
                        </div>

                        <div class="group mb-3">
                            <label for="imagen">Imagen del Artista</label>
                            <input type="file" id="imagen" name="imagen">
                            <p id="imagenUrl"></p>
                        </div>



                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btn_Editar">Actualizar Artista</button>

                </div>
            </div>
        </div>
    </div>


@stop

@section('css')

@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#table-artistas').DataTable();

            //Toast

            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                },
            });




            //Editar
            $('#table-artistas').on('click', '.btnAbrirModalEditar', function() {
                var artistaId = $(this).data('id');
                // Enviar la solicitud AJAX al controlador
                $.ajax({
                    type: 'POST',
                    url: '{{ route('artista.data', ':id') }}'.replace(':id', artistaId),
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            // Manejar los datos del usuario devueltos
                            var artistaData = response.artista;
                            console.log(artistaData);
                            $('#artistaId').val(artistaData.id);
                            $('#nombreE').val(artistaData.nombre);
                            $('#descripcionE').val(artistaData.descripcion);
                            $('#imagenUrl').text('Ruta de la imagen: ' + artistaData.imagen);
                            // Aquí puedes realizar acciones con los datos del usuario, como llenar un formulario modal, etc.
                        } else {
                            // Manejar el caso en que el usuario no existe
                            console.log(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error en la solicitud AJAX:', error);
                    }
                });
            });



            $('#btn_Editar').click(function(e) {
                e.preventDefault();
                // Crea un nuevo objeto FormData
                var formData = new FormData();

                // Agrega manualmente los datos que deseas enviar
                formData.append('id', $('#artistaId').val());
                formData.append('nombre', $('#nombreE').val()); // Ejemplo: campo de nombre
                formData.append('descripcion', $('#descripcionE').val()); // Ejemplo: campo de descripción
                formData.append('imagen', $('#imagen')[0].files[0]); // Ejemplo: campo de imagen

                $.ajax({
                    type: "PUT",
                    url: '/artistas/' + $('#artistaId').val(),
                    data: formData,
                    dataType: "json",
                    processData: false, // Evita que jQuery procese los datos
                    contentType: false, // Evita que jQuery establezca el tipo de contenido
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status === "success") {

                            Toast.fire({
                                icon: "success",
                                title: response.message,
                            });

                            $("#form-registro-artista")[0].reset();
                        }
                    },
                    error: function(response) {
                        if (response.status === 422) {
                            // Verifica si la respuesta tiene el estado 422
                            var errors = response.responseJSON.errors;
                            var errorMessage = "Error al editar el Artista:<br>";

                            // Construye un mensaje de error que incluye todos los errores de validación.
                            $.each(errors, function(key, value) {
                                // Asume que `value` es un array y toma el primer mensaje de error.
                                errorMessage += "-" + value[0] +
                                    "<br>"; // Ajusta según tu estructura de error
                            });

                            Swal.fire({
                                icon: "error",
                                title: "¡INFORMACIÓN!",
                                html: errorMessage,
                            });
                        } else {
                            // Manejo de otros tipos de errores.
                            Toast.fire({
                                icon: "error",
                                title: "Error desconocido al editar el Artista.",
                            });
                        }
                    },
                });

            });

            // Registrar
            //acciones modal

            $('#btnAbrirModal').click(function(e) {
                e.preventDefault();
                $('#form-registro-artista')[0].reset();
            });

            $("#btn_Register").click(function(e) {
                e.preventDefault();


                // Crea un nuevo objeto FormData
                var formData = new FormData();

                // Agrega manualmente los datos que deseas enviar
                formData.append('nombre', $('#nombre').val()); // Ejemplo: campo de nombre
                formData.append('descripcion', $('#descripcion').val()); // Ejemplo: campo de descripción
                formData.append('imagen', $('#imagen')[0].files[0]); // Ejemplo: campo de imagen

                var url = $(this).data("url"); // Obtener la URL de la ruta del atributo data-url


                $.ajax({
                    type: "POST",
                    url: url,
                    data: formData,
                    processData: false, // Evita que jQuery procese los datos
                    contentType: false, // Evita que jQuery establezca el tipo de contenido
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status === "success") {

                            Toast.fire({
                                icon: "success",
                                title: response.message,
                            });

                            $("#form-registro-artista")[0].reset();

                            // setTimeout(() => {
                            //     window.location.href = "/actividades/";
                            // }, 1500);
                        }
                    },
                    error: function(response) {
                        if (response.status === 422) {
                            // Verifica si la respuesta tiene el estado 422
                            var errors = response.responseJSON.errors;
                            var errorMessage = "Error al registrar el Artista:<br>";

                            // Construye un mensaje de error que incluye todos los errores de validación.
                            $.each(errors, function(key, value) {
                                // Asume que `value` es un array y toma el primer mensaje de error.
                                errorMessage += "- " + value[0] +
                                    "<br>"; // Ajusta según tu estructura de error
                            });

                            Swal.fire({
                                icon: "error",
                                title: "¡INFORMACIÓN!",
                                html: errorMessage,
                            });
                        } else {
                            // Manejo de otros tipos de errores.
                            Toast.fire({
                                icon: "error",
                                title: "Error desconocido al registrar el Artista.",
                            });
                        }
                    },
                });
            });




        });
    </script>
@stop
