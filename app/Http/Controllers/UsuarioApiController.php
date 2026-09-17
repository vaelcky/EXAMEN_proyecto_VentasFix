<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Rules\RutValido;

class UsuarioApiController extends Controller
{
    // GET: muestra todos los usuarios
    public function index()
    {
        $usuarios = Usuario::orderBy('created_at', 'desc')->get();
        return response()->json($usuarios, 200);
    }

    // GET:muestra un usuario especifico
    public function show($id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }
        return response()->json($usuario, 200);
    }

    // POST: crea un usuario nuevo
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rut' => ['required', 'string', 'max:20', 'unique:usuarios,rut', new RutValido()],
            'nombre' => 'required|string|max:100|regex:/^[\pL\s]+$/u',
            'apellido' => 'required|string|max:100|regex:/^[\pL\s]+$/u',
            'email' => ['required', 'string', 'max:255', 'unique:usuarios,email', 'regex:/^[a-zA-Z0-9._%+-]+@ventasfix\.cl$/'],
            'password' => 'required|string|min:8',
        ], [
            'rut.required' => 'El rut es obligatorio.',
            'rut.unique' => 'Ese rut ya esta registrado.',
            'nombre.required' => 'El nombre es obligatorio.',
            'apellido.required' => 'El apellido es obligatorio.',
            'nombre.regex' => 'El nombre solo puede contener letras y espacios.',
            'apellido.regex' => 'El apellido solo puede contener letras y espacios.',
            'email.required' => 'El correo es obligatorio.',
            'email.unique' => 'Ese correo ya esta registrado.',
            'email.regex' => 'El correo debe ser del dominio @ventasfix.cl',
            'password.required' => 'La clave es obligatoria.',
            'password.min' => 'La clave debe tener al menos 8 caracteres.',
        ]);
        $validated['password'] = Hash::make($validated['password']);
        $usuario = Usuario::create($validated);
        return response()->json($usuario, 201);
    }

    // PUT/PATCH: actualiza un usuario existente. agrego que el put es para actualizar
    //todo un campo y patch para actualizar el o los campo que querramos (los que eligamos)
    public function update(Request $request, $id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }
        $validated = $request->validate([
            'rut' => ['required', 'string', 'max:20', 'unique:usuarios,rut,' . $id, new RutValido()],
            'nombre' => 'required|string|max:100|regex:/^[\pL\s]+$/u',
            'apellido' => 'required|string|max:100|regex:/^[\pL\s]+$/u',
            'email' => ['required', 'string', 'max:255', 'unique:usuarios,email,' . $id, 'regex:/^[a-zA-Z0-9._%+-]+@ventasfix\.cl$/'],
            'password' => 'required|string|min:8',
        ], [
            'rut.required' => 'El rut es obligatorio.',
            'rut.unique' => 'Ese rut ya esta registrado.',
            'nombre.required' => 'El nombre es obligatorio.',
            'apellido.required' => 'El apellido es obligatorio.',
            'nombre.regex' => 'El nombre solo puede contener letras y espacios.',
            'apellido.regex' => 'El apellido solo puede contener letras y espacios.',
            'email.required' => 'El correo es obligatorio.',
            'email.unique' => 'Ese correo ya esta registrado.',
            'email.regex' => 'El correo debe ser del dominio @ventasfix.cl',
            'password.required' => 'La clave es obligatoria.',
            'password.min' => 'La clave debe tener al menos 8 caracteres.',
        ]);
        $validated['password'] = Hash::make($validated['password']);
        $usuario->update($validated);
        return response()->json($usuario, 200);
    }

    // DELETE:elimina un usuario
    public function destroy($id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }
        $usuario->delete();
        return response()->noContent();
    }
}