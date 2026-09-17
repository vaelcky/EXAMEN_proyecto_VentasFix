<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    //esta es una funcion para registrar un usuario nuevo, con la clave cifrada antes de guardarla
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rut' => 'required|string|max:20|unique:usuarios,rut',
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email' => ['required', 'string', 'max:255', 'unique:usuarios,email', 'regex:/^[a-zA-Z0-9._%+-]+@ventasfix\.cl$/'],
            'password' => 'required|string|min:8',
        ], [
            'email.regex' => 'El correo debe ser del dominio @ventasfix.cl',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $usuario = Usuario::create([
            'rut' => $request->rut,
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        return response()->json([
            'message' => 'Usuario registrado correctamente',
            'usuario' => $usuario,
        ], 201);
    }

    //login para validar las credenciales y devolver un JWT si son correctas
    public function login(Request $request)
    {
        $credenciales = $request->only('email', 'password');

        $token = auth('api')->attempt($credenciales);

        if (!$token) {
            return response()->json([
                'message' => 'Credenciales incorrectas',
            ], 401);
        }
        return response()->json([
            'message' => 'Login exitoso',
            'token' => $token,
            'token_type' => 'bearer',
        ], 200);
    }

    // etso cierra la sesion invalidando el token actual
    public function logout()
    {
        auth('api')->logout();
        return response()->json([
            'message' => 'Sesion cerrada correctamente',
        ], 200);
    }
}