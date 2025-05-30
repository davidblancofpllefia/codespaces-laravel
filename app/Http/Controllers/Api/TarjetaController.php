<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Models\Tarjeta;
use Illuminate\Support\Facades\Auth;

class TarjetaController extends Controller
{
    public function index()
    {
        // Opcional: mostrar todas o solo públicas
        $tarjetas = Tarjeta::all();
        return response()->json(['tarjetas' => $tarjetas], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|max:255',
            'imagen' => 'required|url',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $tarjeta = Tarjeta::create([
            'nombre' => $request->nombre,
            'imagen' => $request->imagen,
            'category_id' => $request->category_id,
            'user_id' => Auth::id(), // Asignar propietario
        ]);

        return response()->json(['tarjeta' => $tarjeta], 201);
    }

    public function show($id)
    {
        $tarjeta = Tarjeta::find($id);

        if (!$tarjeta) {
            return response()->json(['message' => 'Tarjeta no encontrada'], 404);
        }

        return response()->json(['tarjeta' => $tarjeta], 200);
    }

    public function update(Request $request, $id)
    {
        $tarjeta = Tarjeta::find($id);

        if (!$tarjeta) {
            return response()->json(['message' => 'Tarjeta no encontrada'], 404);
        }

        // Verificar que es propietario o admin
        $user = Auth::user();
        if ($tarjeta->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|max:255',
            'imagen' => 'required|url',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $tarjeta->update($request->only(['nombre', 'imagen', 'category_id']));

        return response()->json(['tarjeta' => $tarjeta], 200);
    }

    public function updatePartial(Request $request, $id)
    {
        $tarjeta = Tarjeta::find($id);

        if (!$tarjeta) {
            return response()->json(['message' => 'Tarjeta no encontrada'], 404);
        }

        // Verificar que es propietario o admin
        $user = Auth::user();
        if ($tarjeta->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|max:255',
            'imagen' => 'sometimes|url',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $tarjeta->update($request->only(['nombre', 'imagen', 'category_id']));

        return response()->json(['tarjeta' => $tarjeta], 200);
    }

    public function destroy($id)
    {
        $tarjeta = Tarjeta::find($id);

        if (!$tarjeta) {
            return response()->json(['message' => 'Tarjeta no encontrada'], 404);
        }

        // Verificar que es propietario o admin
        $user = Auth::user();
        if ($tarjeta->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $tarjeta->delete();
        return response()->json(['message' => 'Tarjeta eliminada'], 200);
    }



public function getByCategory($categoryId)
{
    // Validar que categoryId existe y es numérico (opcional pero recomendable)
    if (!is_numeric($categoryId)) {
        return response()->json(['error' => 'ID de categoría inválido'], 400);
    }

    $tarjetas = Tarjeta::where('category_id', $categoryId)->get();

    return response()->json([
        'message' => "Tarjetas de la categoría $categoryId",
        'tarjetas' => $tarjetas
    ], 200);
}

// Listar tarjetas propias del usuario autenticado
public function mytarjetas()
{
    $userId = Auth::id();

    if (!$userId) {
        return response()->json(['error' => 'Usuario no autenticado'], 401);
    }

    $tarjetas = Tarjeta::where('user_id', $userId)->get();

    return response()->json([
        'message' => 'Tus tarjetas',
        'tarjetas' => $tarjetas
    ], 200);
}

// Listar tarjetas públicas (sin propietario)
public function publictarjetas()
{
    $tarjetas = Tarjeta::whereNull('user_id')->get();

    return response()->json([
        'message' => 'Tarjetas públicas',
        'tarjetas' => $tarjetas
    ], 200);
}

// Mostrar todas las tarjetas con relaciones cargadas
public function all()
{
    $tarjetas = Tarjeta::with('user', 'category')->get();

    return response()->json([
        'message' => 'Todas las tarjetas',
        'tarjetas' => $tarjetas
    ], 200);
}

// Eliminar cualquier tarjeta (como admin)
public function adminDestroy(Tarjeta $tarjeta)
{
    $user = Auth::user();
    if (!$user || $user->role !== 'admin') {
        return response()->json(['error' => 'No autorizado'], 403);
    }

    $tarjeta->delete();

    return response()->json(['message' => 'Tarjeta eliminada por admin'], 200);
}

// Editar tarjeta como admin
public function adminUpdate(Request $request, Tarjeta $tarjeta)
{
    $user = Auth::user();
    if (!$user || $user->role !== 'admin') {
        return response()->json(['error' => 'No autorizado'], 403);
    }

    $validated = $request->validate([
        'nombre' => 'sometimes|string|max:255',
        'imagen' => 'sometimes|url', // corregí el nombre a 'imagen' para que coincida con el campo en DB
        'category_id' => 'nullable|exists:categories,id',
    ]);

    $tarjeta->update($validated);

    return response()->json([
        'message' => 'Tarjeta actualizada por admin',
        'tarjeta' => $tarjeta
    ], 200);
}


}
