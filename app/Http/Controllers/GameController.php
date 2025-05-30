<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use Illuminate\Support\Facades\Auth;
use App\Models\Tarjeta;


class GameController extends Controller
{
public function index()
{
    $user = auth('api')->user(); // obtiene el usuario autenticado con guard 'api'
    if (!$user) {
        return response()->json(['error' => 'Usuario no autenticado'], 401);
    }

    $games = Game::where('user_id', $user->id)->get();

    return response()->json([
        'message' => 'Llistat de partides',
        'data' => $games
    ], 200);
}

public function store()
{
    $user = auth('api')->user(); // usa el guard 'api' explícitamente
    if (!$user) {
        return response()->json(['error' => 'Usuario no autenticado'], 401);
    }
    //Agrege lo de arriba para que comprobara que eror me daba y sirviera

    $game = Game::create([
        'user_id' => $user->id,  // usa el id del usuario autenticado
        'clicks' => 0,
        'points' => 0,
        'duration' => null
    ]);

    return response()->json([
        'message' => 'Partida creada',
        'data' => $game
    ], 201);
}

//Es metodo update pero cambaido de nombre
public function finish(Request $request, Game $game)
{
    $user = auth('api')->user(); // O Auth::user() si usas otro guard
    if (!$user) {
        return response()->json(['error' => 'Usuari no autenticat'], 401);
    }

    if ($game->user_id !== $user->id) {
        return response()->json(['error' => 'No autoritzat'], 403);
    }

    $validated = $request->validate([
        'clicks' => 'required|integer|min:0',
        'points' => 'required|integer|min:0',
        'duration' => 'required|integer|min:1',
    ]);

    $game->update($validated);

    return response()->json([
        'message' => 'Partida finalitzada',
        'data' => $game
    ], 200);
}


public function destroy(Game $game)
{
    $user = auth('api')->user(); // Obtener el usuario autenticado usando el guard 'api'
    if (!$user) {
        return response()->json(['error' => 'Usuario no autenticado'], 401);
    }

    // Comprobar si el usuario es propietario o admin
    if ($user->id !== $game->user_id && $user->role !== 'admin') {
        return response()->json(['error' => 'No autorizado'], 403);
    }

    $game->delete();

    return response()->json(['message' => 'Partida eliminada'], 200);
}


public function ranking()
{
    $ranking = Game::select('user_id')
        ->selectRaw('MIN(duration) as best_time')
        ->selectRaw('MIN(clicks) as min_clicks')
        ->selectRaw('MAX(points) as max_points')
        ->groupBy('user_id')
        ->orderBy('best_time')
        ->orderBy('min_clicks')
        ->with('user')
        ->take(5)
        ->get();
    return response()->json([
        'message' => 'Top 5 jugadors',
        'data' => $ranking
    ], 200);
}

public function getGamesByUserId($id)
{
    $games = Game::where('user_id', $id)->get();

    return response()->json([
        'message' => "Partides de l’usuari $id",
        'data' => $games
    ]);
}

}
