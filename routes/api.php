<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('empresa')->group(function () {
    Route::get('/', 'EmpresaController@index');
    Route::get('{id}', 'EmpresaController@show');
    Route::post('', 'EmpresaController@store');
    Route::delete('{id}', 'EmpresaController@destroy');
});

Route::prefix('telefone')->group(function () {
    Route::get('/', 'TelefoneController@index');
    Route::get('{id}', 'TelefoneController@show');
    Route::post('', 'TelefoneController@store');
    Route::delete('{id}', 'TelefoneController@destroy');
});

Route::prefix('evento')->group(function () {
    Route::post('/cadastrar-evento', 'EventoController@cadastrarEvento');
    Route::get('/retornar-administradores/{id}', 'EventoController@retornarAdministradores');
});
