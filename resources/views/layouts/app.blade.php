<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VentasFix | Backoffice</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-slate-100 text-slate-800 min-h-screen">

<nav class="bg-white border-b border-slate-200">
    <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
        <span class="text-lg font-bold text-slate-800">
            Ventas<span class="text-blue-600">Fix</span>
        </span>
        @hasSection('nav-links')
            <div class="flex items-center gap-4">
                <a href="/dashboard" class="text-sm font-semibold text-slate-600 hover:text-blue-600">Dashboard</a>
                <a href="/usuarios" class="text-sm font-semibold text-slate-600 hover:text-blue-600">Usuarios</a>
                <a href="/productos" class="text-sm font-semibold text-slate-600 hover:text-blue-600">Productos</a>
                <a href="/clientes" class="text-sm font-semibold text-slate-600 hover:text-blue-600">Clientes</a>
                <button id="btn-logout" class="text-sm font-semibold text-red-600 hover:text-red-700">Cerrar sesion</button>
            </div>
        @endif
    </div>
</nav>

    <main class="max-w-6xl mx-auto px-6 py-8">
        @yield('contenido')
    </main>

    @vite('resources/js/app.js')
    @yield('scripts')

</body>
</html>