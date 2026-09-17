@extends('layouts.app')

@section('nav-links', 'activo')

@section('contenido')

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Usuarios</h1>
        <x-atoms.boton id="btn-nuevo" type="button" color="blue">+ Nuevo Usuario</x-atoms.boton>
    </div>

    <div id="mensaje-error" class="hidden bg-red-50 text-red-700 text-sm rounded-lg p-3 mb-4"></div>

    <div id="tabla-usuarios" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"></div>

    <!--esta es la parte del formulario -->
    <div id="modal-form" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl p-6 max-w-md w-full">
            <h2 id="form-titulo" class="text-lg font-bold text-slate-900 mb-4">Nuevo Usuario</h2>

            <form id="form-usuario" class="space-y-3">
                <input type="hidden" name="id">

                <x-atoms.campo label="Rut" name="rut" />
                <x-atoms.campo label="Nombre" name="nombre" />
                <x-atoms.campo label="Apellido" name="apellido" />
                <x-atoms.campo label="Email (@ventasfix.cl)" name="email" type="email" />
                <x-atoms.campo label="Contrasena" name="password" type="password" />



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

    const contenedor = document.getElementById('tabla-usuarios');
    const modal = document.getElementById('modal-form');
    const form = document.getElementById('form-usuario');
    const errorBox = document.getElementById('mensaje-error');
    function mostrarErrores(data) {
    // limpiamos los errores anteriores antes de mostrar los nuevos
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

    async function cargarUsuarios() {
        const response = await fetch('/api/usuarios', { headers: headersConToken() });
        const usuarios = await response.json();

        contenedor.innerHTML = '';

        if (usuarios.length === 0) {
            contenedor.innerHTML = '<p class="text-slate-400 col-span-full">No hay usuarios registrados.</p>';
            return;
        }

        usuarios.forEach(function (usuario) {
            const card = document.createElement('div');
            card.className = 'bg-white border border-slate-200 rounded-2xl p-5 shadow-sm';
            card.innerHTML = `
                <h3 class="font-bold text-slate-800">${usuario.nombre} ${usuario.apellido}</h3>
                <p class="text-sm text-slate-500">${usuario.email}</p>
                <p class="text-xs text-slate-400 mt-1">Rut: ${usuario.rut}</p>
                <div class="flex gap-3 mt-4 text-sm">
                    <button class="btn-editar text-amber-600 font-semibold" data-id="${usuario.id}">Editar</button>
                    <button class="btn-eliminar text-red-600 font-semibold" data-id="${usuario.id}">Eliminar</button>
                </div>
            `;
            contenedor.appendChild(card);
        });

        document.querySelectorAll('.btn-editar').forEach(function (btn) {
            btn.addEventListener('click', function () { editarUsuario(this.dataset.id); });
        });
        document.querySelectorAll('.btn-eliminar').forEach(function (btn) {
            btn.addEventListener('click', function () { eliminarUsuario(this.dataset.id); });
        });
    }

    async function editarUsuario(id) {
        const response = await fetch('/api/usuarios/' + id, { headers: headersConToken() });
        const usuario = await response.json();

        document.getElementById('form-titulo').textContent = 'Editar Usuario';
        form.id.value = usuario.id;
        form.rut.value = usuario.rut;
        form.nombre.value = usuario.nombre;
        form.apellido.value = usuario.apellido;
        form.email.value = usuario.email;
        form.password.value = '';

        modal.classList.remove('hidden');
    }
    async function eliminarUsuario(id) {
        if (!confirm('Seguro que quieres eliminar este usuario?')) return;
        await fetch('/api/usuarios/' + id, {
            method: 'DELETE',
            headers: headersConToken(),
        });
        cargarUsuarios();
    }
    document.getElementById('btn-nuevo').addEventListener('click', function () {
        form.reset();
        form.id.value = '';
        document.getElementById('form-titulo').textContent = 'Nuevo Usuario';
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
            rut: form.rut.value,
            nombre: form.nombre.value,
            apellido: form.apellido.value,
            email: form.email.value,
            password: form.password.value,
        };
        const url = id ? '/api/usuarios/' + id : '/api/usuarios';
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
        cargarUsuarios();
    });
    cargarUsuarios();
});
</script>
@endsection