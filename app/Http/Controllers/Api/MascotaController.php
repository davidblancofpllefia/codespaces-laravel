<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mascota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MascotaController extends Controller
{
    // 1. Mostrar SOLO las mascotas del usuario autenticado
    public function index()
    {
        $user = auth()->user();
        $mascotas = Mascota::where('user_id', $user->id)->get();

        return response()->json(['mascotas' => $mascotas], 200);
    }

    // 2. Crear mascota, asignando user_id automáticamente del usuario autenticado
    public function store(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'nom' => 'required|max:255',
            'tipus' => 'required|string',
            'edat' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $mascota = new Mascota();
        $mascota->nom = $request->nom;
        $mascota->tipus = $request->tipus;
        $mascota->edat = $request->edat;
        $mascota->user_id = $user->id;  // IMPORTANTE: asignamos aquí el dueño
        $mascota->save();

        return response()->json(['mascota' => $mascota], 201);
    }

    // 3. Mostrar una mascota SOLO si pertenece al usuario
    public function show($id)
    {
        $user = auth()->user();
        $mascota = Mascota::where('id', $id)->where('user_id', $user->id)->first();

        if (!$mascota) {
            return response()->json(['message' => 'Mascota no encontrada o no autorizada'], 404);
        }

        return response()->json(['mascota' => $mascota], 200);
    }

    // 4. Actualizar mascota (completo) solo si es del usuario
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $mascota = Mascota::where('id', $id)->where('user_id', $user->id)->first();

        if (!$mascota) {
            return response()->json(['message' => 'Mascota no encontrada o no autorizada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nom' => 'required|max:255',
            'tipus' => 'required|string',
            'edat' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $mascota->nom = $request->nom;
        $mascota->tipus = $request->tipus;
        $mascota->edat = $request->edat;
        $mascota->save();

        return response()->json(['mascota' => $mascota], 200);
    }

    // 5. Actualizar parcialmente mascota solo si es del usuario
    public function updatePartial(Request $request, $id)
    {
        $user = auth()->user();
        $mascota = Mascota::where('id', $id)->where('user_id', $user->id)->first();

        if (!$mascota) {
            return response()->json(['message' => 'Mascota no encontrada o no autorizada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nom' => 'sometimes|max:255',
            'tipus' => 'sometimes|string',
            'edat' => 'sometimes|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $mascota->fill($request->all());
        $mascota->save();

        return response()->json(['mascota' => $mascota], 200);
    }

    // 6. Eliminar mascota solo si es del usuario
    public function destroy($id)
    {
        $user = auth()->user();
        $mascota = Mascota::where('id', $id)->where('user_id', $user->id)->first();

        if (!$mascota) {
            return response()->json(['message' => 'Mascota no encontrada o no autorizada'], 404);
        }

        $mascota->delete();
        return response()->json(['message' => 'Mascota eliminada'], 200);
    }
}
