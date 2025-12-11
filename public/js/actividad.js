$(document).ready(function () {
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



    //Acciones
    $("#btn_Register").click(function (e) {
        e.preventDefault();

        var datosFormulario = $("#form_actividad").serialize();
        var url = $(this).data("url"); // Obtener la URL de la ruta del atributo data-url

        $.ajax({
            type: "POST",
            url: url,
            data: datosFormulario,
            success: function (response) {
                if (response.status === "success") {
                    Toast.fire({
                        icon: "success",
                        title: response.message,
                    });

                    $("#form_actividad")[0].reset();

                    setTimeout(() => {
                        window.location.href = "/actividades/";
                    }, 1500);
                }
            },
            error: function (response) {
                if (response.status === 422) {
                    // Verifica si la respuesta tiene el estado 422
                    var errors = response.responseJSON.errors;
                    var errorMessage = "Error al registrar la actividad:<br>";

                    // Construye un mensaje de error que incluye todos los errores de validación.
                    $.each(errors, function (key, value) {
                        // Asume que `value` es un array y toma el primer mensaje de error.
                        errorMessage += "- " + value[0] + "<br>"; // Ajusta según tu estructura de error
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
                        title: "Error desconocido al registrar la actividad.",
                    });
                }
            },
        });
    });

    $("#btn_Edit").click(function (e) {
        e.preventDefault();

        var datosFormulario = $("#form_actividad").serialize();
        datosFormulario += "&_method=PUT";
        var url = $(this).data("url"); // Obtener la URL de la ruta del atributo data-url

        $.ajax({
            type: "POST",
            url: url,
            data: datosFormulario,
            success: function (response) {
                if (response.status === "success") {
                    Toast.fire({
                        icon: "success",
                        title: response.message,
                    });

                    setTimeout(() => {
                        window.location.href = "/actividades/";
                    }, 1500);
                }
            },
            error: function (response) {
                if (response.status === 422) {
                    // Verifica si la respuesta tiene el estado 422
                    var errors = response.responseJSON.errors;
                    var errorMessage = "Error al editar la actividad:<br>";

                    // Construye un mensaje de error que incluye todos los errores de validación.
                    $.each(errors, function (key, value) {
                        // Asume que `value` es un array y toma el primer mensaje de error.
                        errorMessage += "- " + value[0] + "<br>"; // Ajusta según tu estructura de error
                    });

                    // Utiliza SweetAlert para mostrar los errores.
                    Swal.fire({
                        icon: "error",
                        title: "¡INFORMACIÓN!",
                        html: errorMessage,
                    });
                } else {
                    // Manejo de otros tipos de errores.
                    Toast.fire({
                        icon: "error",
                        title: "Error desconocido al editar la actividad",
                    });
                }
            },
        });
    });

    $(document).on("click", "#btn_Delete", function (e) {
        e.preventDefault();

        var url = $(this).data("url");
        var token = $("meta[name='csrf-token']").attr("content");

        // Usando SweetAlert2 para la confirmación
        Swal.fire({
            title: "¿Estás seguro?",
            text: "Esta acción no se puede deshacer.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sí, eliminar!",
            cancelButtonText: "Cancelar",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        _method: "DELETE",
                        _token: token,
                    },
                    success: function (response) {

                        if (response.status === "success") {

                            Swal.fire(
                                "Eliminado!",
                                "El registro ha sido eliminado.",
                                "success"
                            );
                        }

                        // Recarga la tabla o la página si es necesario
                        // $('#table_actividades').DataTable().ajax.reload();

                        setTimeout(() => {
                            window.location.reload(); // Para recargar la página
                        }, 1500);

                    },
                    error: function (xhr) {
                        // Puedes usar SweetAlert2 también para mostrar errores
                        Swal.fire(
                            "Error!",
                            "No se pudo eliminar el registro.",
                            "error"
                        );
                    },
                });
            }
        });
    });

});
