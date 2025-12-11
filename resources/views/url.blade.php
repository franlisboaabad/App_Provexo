<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

   
</head>

<body class="antialiased">
    <div
        class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white">
        <div class="max-w-7xl bg-blue-400 px-10 py-20 rounded-xl text-center flex flex-col items-center">
            <h1 class="text-white text-3xl font-bold mb-2">QR CODE</h1>


            @php
            use SimpleSoftwareIO\QrCode\Facades\QrCode;
        @endphp



            <div class="visible-print text-center">
                <h1>Laravel 7 - QR Code Generator Example</h1>
                {!! QrCode::size(250)->generate('www.google.com') !!}
            </div>

            <p class="text-white mt-2 text-sm">Ingresa a laravel.com</p>
        </div>
    </div>
</body>

</html>
