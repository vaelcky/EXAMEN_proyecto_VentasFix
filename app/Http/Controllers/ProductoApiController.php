<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoApiController extends Controller
{
    // GET: lista todos los productos
    public function index()
    {
        $productos = Producto::orderBy('created_at', 'desc')->get();
        return response()->json($productos, 200);
    }

    // GET: muestra un producto especifico
    public function show($id)
    {
        $producto = Producto::find($id);
        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }
        return response()->json($producto, 200);
    }

    // POST: crea un producto nuevo
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => 'required|string|max:50|unique:productos,sku',
            'nombre' => 'required|string|max:150',
            'descripcion_corta' => 'required|string|max:255',
            'descripcion_larga' => 'required|string',
            'imagen' => 'required|string',
            'precio_neto' => 'required|numeric|min:1',
            'stock_actual' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0|lt:stock_bajo',
            'stock_bajo' => 'required|integer|min:0',
            'stock_alto' => 'required|integer|min:0',
        ], [
            'sku.required' => 'El sku es obligatorio.',
            'sku.unique' => 'Ese sku ya esta registrado.',
            'nombre.required' => 'El nombre es obligatorio.',
            'descripcion_corta.required' => 'La descripcion corta es obligatoria.',
            'descripcion_larga.required' => 'La descripcion larga es obligatoria.',
            'imagen.required' => 'La imagen es obligatoria.',
            'precio_neto.required' => 'El precio neto es obligatorio.',
            'precio_neto.min' => 'El precio neto debe ser mayor que cero.',
            'stock_actual.required' => 'El stock actual es obligatorio.',
            'stock_minimo.required' => 'El stock minimo es obligatorio.',
            'stock_minimo.lt' => 'El stock minimo debe ser menor que el stock bajo.',
            'stock_bajo.required' => 'El stock bajo es obligatorio.',
            'stock_alto.required' => 'El stock alto es obligatorio.',
        ]);

        //ojo el precio de venta se calcula solo, así: precio neto + 19% de iva
        $validated['precio_venta'] = round($validated['precio_neto'] * 1.19, 2);
        $producto = Producto::create($validated);
        return response()->json($producto, 201);
    }

    // PUT/PATCH:actualiza un producto existente
    public function update(Request $request, $id)
    {
        $producto = Producto::find($id);
        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $validated = $request->validate([
            'sku' => 'required|string|max:50|unique:productos,sku,' . $id,
            'nombre' => 'required|string|max:150',
            'descripcion_corta' => 'required|string|max:255',
            'descripcion_larga' => 'required|string',
            'imagen' => 'required|string',
            'precio_neto' => 'required|numeric|min:1',
            'stock_actual' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0|lt:stock_bajo',
            'stock_bajo' => 'required|integer|min:0',
            'stock_alto' => 'required|integer|min:0',
        ], [
            'sku.required' => 'El sku es obligatorio.',
            'sku.unique' => 'Ese sku ya esta registrado.',
            'nombre.required' => 'El nombre es obligatorio.',
            'descripcion_corta.required' => 'La descripcion corta es obligatoria.',
            'descripcion_larga.required' => 'La descripcion larga es obligatoria.',
            'imagen.required' => 'La imagen es obligatoria.',
            'precio_neto.required' => 'El precio neto es obligatorio.',
            'precio_neto.min' => 'El precio neto debe ser mayor que cero.',
            'stock_actual.required' => 'El stock actual es obligatorio.',
            'stock_minimo.required' => 'El stock minimo es obligatorio.',
            'stock_minimo.lt' => 'El stock minimo debe ser menor que el stock bajo.',
            'stock_bajo.required' => 'El stock bajo es obligatorio.',
            'stock_alto.required' => 'El stock alto es obligatorio.',
        ]);
        $validated['precio_venta'] = round($validated['precio_neto'] * 1.19, 2);
        $producto->update($validated);
        return response()->json($producto, 200);
    }

    // DELETE:elimina un producto uwu
    public function destroy($id)
    {
        $producto = Producto::find($id);
        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }
        $producto->delete();
        return response()->noContent();
    }
}