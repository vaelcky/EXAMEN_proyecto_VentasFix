@extends('layouts.app')

@section('nav-links', 'activo')

@section('contenido')

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Clientes</h1>
        <x-atoms.boton id="btn-nuevo" type="button" color="blue">+ Nuevo Cliente</x-atoms.boton>
    </div>

    <div id="mensaje-error" class="hidden bg-red-50 text-red-700 text-sm rounded-lg p-3 mb-4"></div>

    <div id="tabla-clientes" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"></div>

    <div id="modal-form" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl p-6 max-w-md w-full my-8">
            <h2 id="form-titulo" class="text-lg font-bold text-slate-900 mb-4">Nuevo Cliente</h2>

            <form id="form-cliente" class="space-y-3">
                <input type="hidden" name="id">

                <x-atoms.campo label="Rut empresa" name="rut_empresa" />
                <x-atoms.campo label="Rubro" name="rubro" />
                <x-atoms.campo label="Razon social" name="razon_social" />
                <x-atoms.campo label="Telefono" name="telefono" />
                <x-atoms.campo label="Direccion" name="direccion" />
                <x-atoms.campo label="Nombre de contacto" name="nombre_contacto" />
                <x-atoms.campo label="Email de contacto" name="email_contacto" type="email" />

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

    const contenedor = document.getElementById('tabla-clientes');
    const modal = document.getElementById('modal-form');
    const form = document.getElementById('form-cliente');
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

    async function cargarClientes() {
        const response = await fetch('/api/clientes', { headers: headersConToken() });

        if (!response.ok) {
            if (response.status === 401) {
                localStorage.removeItem('token');
                window.location.href = '/login';
                return;
            }
            contenedor.innerHTML = '<p class="text-red-500 col-span-full">Error al cargar los clientes.</p>';
            return;
        }

        const clientes = await response.json();

        contenedor.innerHTML = '';

        if (clientes.length === 0) {
            contenedor.innerHTML = '<p class="text-slate-400 col-span-full">No hay clientes registrados.</p>';
            return;
        }

        clientes.forEach(function (cliente) {
            const card = document.createElement('div');
            card.className = 'bg-white border border-slate-200 rounded-2xl p-5 shadow-sm';
            card.innerHTML = `
                <h3 class="font-bold text-slate-800">${cliente.razon_social}</h3>
                <p class="text-xs text-slate-400">Rut: ${cliente.rut_empresa}</p>
                <p class="text-sm text-slate-500 mt-2">${cliente.rubro}</p>
                <p class="text-xs text-slate-400 mt-2">Contacto: ${cliente.nombre_contacto}</p>
                <p class="text-xs text-slate-400">${cliente.email_contacto}</p>
                <div class="flex gap-3 mt-4 text-sm">
                    <button class="btn-editar text-amber-600 font-semibold" data-id="${cliente.id}">Editar</button>
                    <button class="btn-eliminar text-red-600 font-semibold" data-id="${cliente.id}">Eliminar</button>
                </div>
            `;
            contenedor.appendChild(card);
        });

        document.querySelectorAll('.btn-editar').forEach(function (btn) {
            btn.addEventListener('click', function () { editarCliente(this.dataset.id); });
        });
        document.querySelectorAll('.btn-eliminar').forEach(function (btn) {
            btn.addEventListener('click', function () { eliminarCliente(this.dataset.id); });
        });
    }

    async function editarCliente(id) {
        const response = await fetch('/api/clientes/' + id, { headers: headersConToken() });
        const cliente = await response.json();

        document.getElementById('form-titulo').textContent = 'Editar Cliente';
        form.id.value = cliente.id;
        form.rut_empresa.value = cliente.rut_empresa;
        form.rubro.value = cliente.rubro;
        form.razon_social.value = cliente.razon_social;
        form.telefono.value = cliente.telefono;
        form.direccion.value = cliente.direccion;
        form.nombre_contacto.value = cliente.nombre_contacto;
        form.email_contacto.value = cliente.email_contacto;

        modal.classList.remove('hidden');
    }

    async function eliminarCliente(id) {
        if (!confirm('Seguro que quieres eliminar este cliente?')) return;

        await fetch('/api/clientes/' + id, {
            method: 'DELETE',
            headers: headersConToken(),
        });

        cargarClientes();
    }

    document.getElementById('btn-nuevo').addEventListener('click', function () {
        form.reset();
        form.id.value = '';
        document.getElementById('form-titulo').textContent = 'Nuevo Cliente';
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
            rut_empresa: form.rut_empresa.value,
            rubro: form.rubro.value,
            razon_social: form.razon_social.value,
            telefono: form.telefono.value,
            direccion: form.direccion.value,
            nombre_contacto: form.nombre_contacto.value,
            email_contacto: form.email_contacto.value,
        };

        const url = id ? '/api/clientes/' + id : '/api/clientes';
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
        cargarClientes();
    });

    cargarClientes();
});
</script>
@endsection