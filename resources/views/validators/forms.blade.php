<!-- resources/views/cliente/index.blade.php -->

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<!-- resources/views/cliente/index.blade.php -->

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Resto de tu contenido de la vista -->

<script>
    // Oculta la alerta después de un cierto tiempo (por ejemplo, 3 segundos)
    setTimeout(() => {
        document.querySelector('.alert').style.display = 'none';
    }, 5000); // Cambia el valor de '3000' al tiempo deseado en milisegundos
</script>
