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


    $('#btn_Register').click(function (e) {
        e.preventDefault();

        var datosFormulario = $("#form_tarea").serialize();
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

                    $("#form_tarea")[0].reset();

                    setTimeout(() => {
                        window.location.href = "/tareas/";
                    }, 1500);
                }

            },
            error: function (response) {
                if (response.status === 422) {
                    // Verifica si la respuesta tiene el estado 422
                    var errors = response.responseJSON.errors;
                    var errorMessage = "Error al registrar la tarea:<br>";

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
                        title: "Error desconocido al registrar la tarea."
                    });
                }
            },
        });

    });
});
