@extends('layouts.app')

@section('nav-links', 'activo')

@section('contenido')

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Productos</h1>
        <x-atoms.boton id="btn-nuevo" type="button" color="blue">+ Nuevo Producto</x-atoms.boton>
    </div>

    <div id="mensaje-error" class="hidden bg-red-50 text-red-700 text-sm rounded-lg p-3 mb-4"></div>

    <div id="tabla-productos" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"></div>

    <div id="modal-form" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl p-6 max-w-md w-full my-8">
            <h2 id="form-titulo" class="text-lg font-bold text-slate-900 mb-4">Nuevo Producto</h2>

            <form id="form-producto" class="space-y-3">
                <input type="hidden" name="id">

                <x-atoms.campo label="Sku" name="sku" />
                <x-atoms.campo label="Nombre" name="nombre" />
                <x-atoms.campo label="Descripcion corta" name="descripcion_corta" />
                <x-atoms.campo label="Descripcion larga" name="descripcion_larga" type="textarea" />
                <x-atoms.campo label="Imagen (ruta)" name="imagen" />
                <x-atoms.campo label="Precio neto" name="precio_neto" type="number" />
                <x-atoms.campo label="Stock actual" name="stock_actual" type="number" />
                <x-atoms.campo label="Stock minimo" name="stock_minimo" type="number" />
                <x-atoms.campo label="Stock bajo" name="stock_bajo" type="number" />
                <x-atoms.campo label="Stock alto" name="stock_alto" type="number" />

                <div class="flex gap-3 pt-2">
                    <div class="flex-1">
                        <x-atoms.boton type="submit" color="blue" ancho="full">Guardar</x-atoms.boton>
                    </div>
                    <div class="flex-1">
                        <x-atoms.boton id="btn-cancelar" type="button" color="outline" ancho="full">Cancelar</x-atoms.boton>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    protegerPagina();

    const contenedor = document.getElementById('tabla-productos');
    const modal = document.getElementById('modal-form');
    const form = document.getElementById('form-producto');
    const errorBox = document.getElementById('mensaje-error');

    function mostrarErrores(data) {
        document.querySelectorAll('[id^="error-"]').forEach(function (el) {
            el.classList.add('hidden');
            el.textContent = '';
        });
        if (data.errors) {
            Object.keys(data.errors).forEach(function (campo) {
                const el = document.getElementById('error-' + campo);
                if (el) {
                    el.textContent = data.errors[campo][0];
                    el.classList.remove('hidden');
                }
            });
        } else if (data.message) {
            errorBox.textContent = data.message;
            errorBox.classList.remove('hidden');
        }
    }

    async function cargarProductos() {
        const response = await fetch('/api/productos', { headers: headersConToken() });

        if (!response.ok) {
            if (response.status === 401) {
                localStorage.removeItem('token');
                window.location.href = '/login';
                return;
            }
            contenedor.innerHTML = '<p class="text-red-500 col-span-full">Error al cargar los productos.</p>';
            return;
        }

        const productos = await response.json();

        contenedor.innerHTML = '';

        if (productos.length === 0) {
            contenedor.innerHTML = '<p class="text-slate-400 col-span-full">No hay productos registrados.</p>';
            return;
        }
        productos.forEach(function (producto) {
            const card = document.createElement('div');
            card.className = 'bg-white border border-slate-200 rounded-2xl p-5 shadow-sm';
            card.innerHTML = `
                <h3 class="font-bold text-slate-800">${producto.nombre}</h3>
                <p class="text-xs text-slate-400">Sku: ${producto.sku}</p>
                <p class="text-sm text-slate-500 mt-2">${producto.descripcion_corta}</p>
                <p class="text-sm font-semibold text-blue-600 mt-2">Venta: $${producto.precio_venta}</p>
                <p class="text-xs text-slate-400">Stock actual: ${producto.stock_actual}</p>
                <div class="flex gap-3 mt-4 text-sm">
                    <button class="btn-editar text-amber-600 font-semibold" data-id="${producto.id}">Editar</button>
                    <button class="btn-eliminar text-red-600 font-semibold" data-id="${producto.id}">Eliminar</button>
                </div>
            `;
            contenedor.appendChild(card);
        });
        document.querySelectorAll('.btn-editar').forEach(function (btn) {
            btn.addEventListener('click', function () { editarProducto(this.dataset.id); });
        });
        document.querySelectorAll('.btn-eliminar').forEach(function (btn) {
            btn.addEventListener('click', function () { eliminarProducto(this.dataset.id); });
        });
    }

    async function editarProducto(id) {
        const response = await fetch('/api/productos/' + id, { headers: headersConToken() });
        const producto = await response.json();

        document.getElementById('form-titulo').textContent = 'Editar Producto';
        form.id.value = producto.id;
        form.sku.value = producto.sku;
        form.nombre.value = producto.nombre;
        form.descripcion_corta.value = producto.descripcion_corta;
        form.descripcion_larga.value = producto.descripcion_larga;
        form.imagen.value = producto.imagen;
        form.precio_neto.value = producto.precio_neto;
        form.stock_actual.value = producto.stock_actual;
        form.stock_minimo.value = producto.stock_minimo;
        form.stock_bajo.value = producto.stock_bajo;
        form.stock_alto.value = producto.stock_alto;

        modal.classList.remove('hidden');
    }

    async function eliminarProducto(id) {
        if (!confirm('Seguro que quieres eliminar este producto?')) return;
        await fetch('/api/productos/' + id, {
            method: 'DELETE',
            headers: headersConToken(),
        });
        cargarProductos();
    }

    document.getElementById('btn-nuevo').addEventListener('click', function () {
        form.reset();
        form.id.value = '';
        document.getElementById('form-titulo').textContent = 'Nuevo Producto';
        modal.classList.remove('hidden');
    });
    document.getElementById('btn-cancelar').addEventListener('click', function () {
        modal.classList.add('hidden');
    });
    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        errorBox.classList.add('hidden');

        const id = form.id.value;
        const datos = {
            sku: form.sku.value,
            nombre: form.nombre.value,
            descripcion_corta: form.descripcion_corta.value,
            descripcion_larga: form.descripcion_larga.value,
            imagen: form.imagen.value,
            precio_neto: form.precio_neto.value,
            stock_actual: form.stock_actual.value,
            stock_minimo: form.stock_minimo.value,
            stock_bajo: form.stock_bajo.value,
            stock_alto: form.stock_alto.value,
        };

        const url = id ? '/api/productos/' + id : '/api/productos';
        const metodo = id ? 'PUT' : 'POST';

        const response = await fetch(url, {
            method: metodo,
            headers: headersConToken(),
            body: JSON.stringify(datos),
        });

        const data = await response.json();

        if (!response.ok) {
            mostrarErrores(data);
            return;
        }
        modal.classList.add('hidden');
        cargarProductos();
    });

    cargarProductos();
});
</script>
@endsection