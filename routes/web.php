<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/usuarios', function () {
    return view('usuarios');
})->name('usuarios');

Route::get('/productos', function () {
    return view('productos');
})->name('productos');

Route::get('/clientes', function () {
    return view('clientes');
})->name('clientes');