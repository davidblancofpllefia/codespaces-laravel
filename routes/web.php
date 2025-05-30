<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/peliculas', function () {
    return view('peliculas');
});
//Suma de dos numeros
// Esta ruta muestra un formulario para sumar dos números
Route::get('/suma', function () {
    return view('suma');
})->name('suma');

// este muestra y hace el calculo de la suma y muestra el resultado
Route::post('/suma', function (Request $request) {
    $num1 = $request->input('num1');
    $num2 = $request->input('num2');
    $resultado = $num1 + $num2;

    return view('suma', ['resultado' => $resultado]);
});