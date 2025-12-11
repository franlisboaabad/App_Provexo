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

        var proyectoData = {
            nombre_proyecto: $('#nombre_proyecto').val(),
            cliente_id: $('#cliente_id').val(),
            usuarios: $('#usuarios').val(), // Obtén los usuarios seleccionados
        };

        // var datosFormulario = $("#form_proyecto").serialize();
        var url = $(this).data("url"); // Obtener la URL de la ruta del atributo data-url

        $.ajax({
            type: "POST",
            url: url,
            data: proyectoData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                if (response.status === "success") {

                    Toast.fire({
                        icon: "success",
                        title: response.message,
                    });

                    $("#form_proyecto")[0].reset();

                    setTimeout(() => {
                        window.location.href = "/proyectos/";
                    }, 1500);
                }
            },
            error: function (response) {
                if (response.status === 422) {
                    // Verifica si la respuesta tiene el estado 422
                    var errors = response.responseJSON.errors;
                    var errorMessage = "Error al registrar el proyecto:<br>";

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
                        title: "Error desconocido al registrar el proyecto.",
                    });
                }
            },
        });
    });


    $('#btn_Edit').click(function (e) {
        e.preventDefault();

        var datosFormulario = $("#form_proyecto").serialize();
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
                        window.location.href = "/proyectos/";
                    }, 1500);
                }
            },
            error: function (response) {
                if (response.status === 422) {
                    // Verifica si la respuesta tiene el estado 422
                    var errors = response.responseJSON.errors;
                    var errorMessage = "Error al registrar el proyecto:<br>";

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
                        title: "Error desconocido al editar el proyecto.",
                    });
                }
            },
        });

    });


});
