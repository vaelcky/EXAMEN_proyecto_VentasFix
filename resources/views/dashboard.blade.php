@extends('layouts.app')

@section('nav-links', 'activo')


@section('contenido')

    <h1 class="text-2xl font-bold text-slate-900 mb-6">Dashboard</h1>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <p class="text-sm text-slate-500">Usuarios</p>
            <p id="conteo-usuarios" class="text-4xl font-extrabold text-blue-600 mt-2">...</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <p class="text-sm text-slate-500">Productos</p>
            <p id="conteo-productos" class="text-4xl font-extrabold text-blue-600 mt-2">...</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <p class="text-sm text-slate-500">Clientes</p>
            <p id="conteo-clientes" class="text-4xl font-extrabold text-blue-600 mt-2">...</p>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
    protegerPagina();

    async function cargarConteo(endpoint, elementoId) {
        try {
            const response = await fetch('/api/' + endpoint, {
                headers: headersConToken(),
            });
            const data = await response.json();
            document.getElementById(elementoId).textContent = data.length;
        } catch (error) {
            document.getElementById(elementoId).textContent = '-';
        }
    }

    cargarConteo('usuarios', 'conteo-usuarios');
    cargarConteo('productos', 'conteo-productos');
    cargarConteo('clientes', 'conteo-clientes');
});
</script>
@endsection