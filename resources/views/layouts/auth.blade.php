<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso - LiteConta</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <img src="{{ asset('img/logo-liteconta.png') }}" alt="LiteConta" class="mx-auto w-24 mb-4">
            <h1 class="text-2xl font-bold text-slate-800 italic">LiteConta</h1>
        </div>
        
        <div class="bg-white p-8 rounded-2xl shadow-xl border border-gray-200">
            @yield('content')
        </div>
    </div>
</body>
</html>