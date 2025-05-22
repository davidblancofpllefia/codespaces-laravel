<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsUserAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       if(auth('api')->user()){
        return $next($request); //puede pasar caballero
       } else {
        return response()->json([
            'message' => 'Token Invalida y no eres admin, dele pa afuera'
        ], 401);

       }
}
}
