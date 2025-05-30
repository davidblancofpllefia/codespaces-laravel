<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;



class AuthController extends Controller
{

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'role' => 'required|string|in:admin,user',
            'email' => 'required|email|string|max:100|unique:users',
            'password' => 'required|string|min:5|confirmed',


        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        $user = User::create([
            'name' => $request->get ('name'),
            'email' => $request->get ('email'),
            'role' => $request->get('role'),
            'password' => bcrypt($request->get ('password')),

        ]);
        return response()->json([
            'message' => 'User created successfully',
         'data' => $user],
         201);



    }


    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|string|max:100',
            'password' => 'required|string|min:5',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 422);
        }
        $credentials = $request->only('email', 'password');

        try {
            if(! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['message' => 'Invalid Credenciales'],
                 401);
            }
            return response()->json(['message' => 'Login successful',
             'token' => $token], 200);

        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token',
             'message' => $e->getMessage()], 500);
        }
    }

// Removed duplicate getUser() method




    public function logout()
    {
        try{
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json([
                'message'=> 'User Logged out sucefuly'
            ], 200);
        }catch (JWTException $e){
            return response()->json ([
                'message' => 'No se pudo Logear'
            ],500);
        }
    }




    //Crud de users
public function getUser()
{
    $user = JWTAuth::parseToken()->authenticate();

    return response()->json([
        'message' => 'User conseguido caramba',
        'data' => $user,
    ], 200);
}

    public function all()
    {
        $users = User::all();

        return response()->json([
            'message' => 'Todos los usuarios',
            'data' => $users,
        ], 200);
    }

    public function getUserById($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'Usuario no encontrado',
            ], 404);
        }

        return response()->json([
            'message' => 'Usuario encontrado',
            'data' => $user,
        ], 200);
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'Usuario no encontrado',
            ], 404);
        }

        $user->update($request->only(['name', 'email', 'role']));

        return response()->json([
            'message' => 'Usuario actualizado',
            'data' => $user,
        ], 200);
    }

    public function adminDestroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'Usuario no encontrado',
            ], 404);
        }

        $user->delete();

        return response()->json([
            'message' => 'Usuario eliminado correctamente',
        ], 200);
    }









}
