@extends('layouts.app')

@section('contenido')

    <div class="min-h-[70vh] flex items-center justify-center">
        <div class="bg-white border border-slate-200 rounded-2xl p-8 max-w-md w-full shadow-sm">

            <h1 class="text-2xl font-bold text-slate-900 mb-1">Iniciar Sesion</h1>
            <p class="text-slate-500 text-sm mb-6">Backoffice VentasFix</p>

            <div id="mensaje-error" class="hidden bg-red-50 text-red-700 text-sm rounded-lg p-3 mb-4"></div>

            <form id="form-login" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Correo</label>
                    <input type="email" name="email" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Contrasena</label>
                    <input type="password" name="password" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>
                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-lg transition">
                    Ingresar
                </button>
            </form>

        </div>
    </div>

@endsection

@section('scripts')
<script>
    document.getElementById('form-login').addEventListener('submit', async function (e) {
        e.preventDefault();

        const errorBox = document.getElementById('mensaje-error');
        errorBox.classList.add('hidden');

        const datos = {
            email: this.email.value,
            password: this.password.value,
        };

        try {
            const response = await fetch('/api/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(datos),
            });

            const data = await response.json();

            if (!response.ok) {
                errorBox.textContent = data.message || 'No se pudo iniciar sesion';
                errorBox.classList.remove('hidden');
                return;
            }

            localStorage.setItem('token', data.token);
            window.location.href = '/dashboard';

        } catch (error) {
            errorBox.textContent = 'No se pudo conectar con el servidor';
            errorBox.classList.remove('hidden');
        }
    });
</script>
@endsection