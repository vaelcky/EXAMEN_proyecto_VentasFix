window.protegerPagina = function () {
    if (!localStorage.getItem('token')) {
        window.location.href = '/login';
    }
};

window.headersConToken = function () {
    return {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': 'Bearer ' + localStorage.getItem('token'),
    };
};

document.addEventListener('DOMContentLoaded', function () {
    const btnLogout = document.getElementById('btn-logout');

    if (btnLogout) {
        btnLogout.addEventListener('click', async function () {
            const token = localStorage.getItem('token');

            try {
                await fetch('/api/logout', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json',
                    },
                });
            } catch (error) {
                // aunque falle la llamada al servidor, limpiamos el token local
            }

            localStorage.removeItem('token');
            window.location.href = '/login';
        });
    }
});