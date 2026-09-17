<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use App\Rules\RutValido;

class ClienteApiController extends Controller
{
    // GET: lista todos los clientes
    public function index()
    {
        $clientes = Cliente::orderBy('created_at', 'desc')->get();
        return response()->json($clientes, 200);
    }

    // GET: muestra un cliente especifico
    public function show($id)
    {
        $cliente = Cliente::find($id);
        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }
        return response()->json($cliente, 200);
    }

    // POST: crea un cliente nuevo
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rut_empresa' => ['required', 'string', 'max:20', 'unique:clientes,rut_empresa', new RutValido()],
            'rubro' => 'required|string|max:100',
            'razon_social' => 'required|string|max:150',
            'telefono' => 'required|string|max:20|regex:/^[0-9+\-\s]+$/',
            'direccion' => 'required|string|max:255',
            'nombre_contacto' => 'required|string|max:150|regex:/^[\pL\s]+$/u',
            'email_contacto' => 'required|email|max:255',
        ], [
            'rut_empresa.required' => 'El rut de la empresa es obligatorio.',
            'rut_empresa.unique' => 'Ese rut de empresa ya esta registrado.',
            'rubro.required' => 'El rubro es obligatorio.',
            'nombre_contacto.regex' => 'El nombre de contacto solo puede contener letras y espacios.',
            'telefono.regex' => 'El telefono solo puede contener numeros, espacios, + y -.',
            'razon_social.required' => 'La razon social es obligatoria.',
            'telefono.required' => 'El telefono es obligatorio.',
            'direccion.required' => 'La direccion es obligatoria.',
            'nombre_contacto.required' => 'El nombre de contacto es obligatorio.',
            'email_contacto.required' => 'El correo de contacto es obligatorio.',
            'email_contacto.email' => 'El correo de contacto debe ser valido.',
        ]);
        $cliente = Cliente::create($validated);
        return response()->json($cliente, 201);
    }

    // PUT/PATCH: actualiza un cliente existente
    public function update(Request $request, $id)
    {
        $cliente = Cliente::find($id);
        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }
        $validated = $request->validate([
            'rut_empresa' => ['required', 'string', 'max:20', 'unique:clientes,rut_empresa,' . $id, new RutValido()],
            'rubro' => 'required|string|max:100',
            'razon_social' => 'required|string|max:150',
            'telefono' => 'required|string|max:20|regex:/^[0-9+\-\s]+$/',
            'direccion' => 'required|string|max:255',
            'nombre_contacto' => 'required|string|max:150|regex:/^[\pL\s]+$/u',
            'email_contacto' => 'required|email|max:255',
        ], [
            'rut_empresa.required' => 'El rut de la empresa es obligatorio.',
            'rut_empresa.unique' => 'Ese rut de empresa ya esta registrado.',
            'rubro.required' => 'El rubro es obligatorio.',
            'nombre_contacto.regex' => 'El nombre de contacto solo puede contener letras y espacios.',
            'telefono.regex' => 'El telefono solo puede contener numeros, espacios, + y -.',
            'razon_social.required' => 'La razon social es obligatoria.',
            'telefono.required' => 'El telefono es obligatorio.',
            'direccion.required' => 'La direccion es obligatoria.',
            'nombre_contacto.required' => 'El nombre de contacto es obligatorio.',
            'email_contacto.required' => 'El correo de contacto es obligatorio.',
            'email_contacto.email' => 'El correo de contacto debe ser valido.',
        ]);
        $cliente->update($validated);
        return response()->json($cliente, 200);
    }

    // DELETE: elimina un cliente
    public function destroy($id)
    {
        $cliente = Cliente::find($id);
        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }
        $cliente->delete();
        return response()->noContent();
    }
}