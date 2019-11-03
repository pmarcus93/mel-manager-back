<?php

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

use Illuminate\Support\Facades\Route;

Route::prefix('usuario')->group(function () {
    Route::post('/cadastrar-usuario', 'UsuarioController@cadastrarUsuario');
    Route::post('/editar-usuario', 'UsuarioController@editarUsuario');
    Route::get('/retornar-usuario-email/{email}', 'UsuarioController@retornarUsuarioEmail');
    Route::get('/retornar-usuario/{user_id}', 'UsuarioController@retornarUsuario');
});

Route::prefix('empresa')->group(function () {
    Route::post('/cadastrar-empresa', 'EmpresaController@cadastrarEmpresa');
    Route::post('/editar-empresa', 'EmpresaController@editarEmpresa');
    Route::post('/vincular-evento-empresa', 'EmpresaController@vincularEventoEmpresa');
    Route::post('/desvincular-evento-empresa', 'EmpresaController@desvincularEventoEmpresa');
    Route::get('/retornar-empresa/{empresa_id}','EmpresaController@retornarEmpresa');
});

Route::prefix('evento')->group(function () {
    Route::get('/retornar-administradores/{evento_id}', 'EventoController@retornarAdministradores');
    Route::get('/retornar-edicoes-evento/{evento_id}', 'EventoController@retornarEdicoesEvento');
    Route::post('/cadastrar-evento', 'EventoController@cadastrarEvento');
    Route::post('/editar-evento', 'EventoController@editarEvento');
    Route::post('/vincular-administrador-evento/', 'EventoController@vincularAdministradorEvento');
    Route::post('/desvincular-administrador-evento/', 'EventoController@desvincularAdministradorEvento');
    Route::get('/retornar-edicoes-evento-usuario/{user_id}', 'EventoController@retornarEdicoesEventoUsuario');
    Route::get('/retornar-evento/{evento_id}', 'EventoController@retornarEvento');
});

Route::prefix('evento-edicao/')->group(function (){
    Route::get('retornar-evento-edicao/{eventoedicao_id}', 'EventoEdicaoController@retornarEventoEdicao');
    Route::post('cadastrar-evento-edicao', 'EventoEdicaoController@cadastrarEventoEdicao');
    Route::post('editar-evento-edicao', 'EventoEdicaoController@editarEventoEdicao');
    Route::post('remover-evento-edicao', 'EventoEdicaoController@removerEventoEdicao');
});
