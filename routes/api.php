<?php

use App\Http\Controllers\UsuarioController;
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

Route::prefix('usuario')->group(function(){
    Route::get('/{nome}','UsuarioController@retornarUsuarioNome');
    Route::get('/{e-mail}','UsuarioController@retornarUsuarioEmail');
    Route::post('/cadastrar-usuario','UsuarioController@cadastrarUsuario');
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
    Route::post('/cadastrar-edicao-evento', 'EventoController@cadastrarEdicaoEvento');
    Route::get('/retornar-administradores/{id}', 'EventoController@retornarAdministradores');
    Route::get('/retornar-edicoes-evento/{id}', 'EventoController@retornarEdicoesEvento');
});
