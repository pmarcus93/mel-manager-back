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
    Route::post('/cadastrar-telefone-usuario', 'UsuarioController@cadastrarTelefone');
    Route::post('/editar-telefone-usuario', 'UsuarioController@editarTelefone');
    Route::delete('/excluir-telefone-usuario/{user_id}&{telefone_id}', 'UsuarioController@removerTelefone');
    Route::get('/retornar-usuario-nome-email/{search}&{qtd}', 'UsuarioController@retornarUsuarioPorNomeEmail');
    Route::get('/retornar-usuario-id/{id}', 'UsuarioController@retornarUsuarioPorID');
});

Route::prefix('empresa')->group(function () {
    Route::post('/cadastrar-empresa', 'EmpresaController@cadastrarEmpresa');
    Route::post('/editar-empresa', 'EmpresaController@editarEmpresa');
    Route::post('/vincular-evento-empresa', 'EmpresaController@vincularEventoEmpresa');
    Route::post('/desvincular-evento-empresa', 'EmpresaController@desvincularEventoEmpresa');
    Route::get('/retornar-empresa/{id}','EmpresaController@retornarEmpresa');
    Route::get('/retornar-eventos-empresa/{empresa_id}','EmpresaController@retornarEventosDeEmpresa');
    Route::post('/cadastrar-telefone-empresa', 'EmpresaController@cadastrarTelefone');
    Route::post('/editar-telefone-empresa', 'EmpresaController@editarTelefone');
    Route::delete('/excluir-telefone-empresa', 'EmpresaController@removerTelefone');

});

Route::prefix('categoria')->group(function () {
    Route::post('/cadastrar-categoria', 'CategoriaController@cadastrarCategoria');
    Route::post('/editar-categoria', 'CategoriaController@editarCategoria');
    Route::delete('/deletar-categoria/{id}','CategoriaController@desativarCategoria');
    Route::get('/retornar-categoria/{id}','CategoriaController@retornarCategoriaPorID');
    Route::get('/retornar-categorias','CategoriaController@retornarTodasCategorias');
});

Route::prefix('evento')->group(function () {
    Route::get('/retornar-administradores/{id}', 'EventoController@retornarAdministradores');
    Route::get('/retornar-edicoes-evento/{id}', 'EventoController@retornarEdicoesEvento');
    Route::get('/retornar-edicao-evento/{edicao_id}', 'EventoController@retornarEdicaoEvento');
    Route::post('/cadastrar-evento', 'EventoController@cadastrarEvento');
    Route::post('/cadastrar-edicao-evento', 'EventoController@cadastrarEdicaoEvento');
    Route::post('/vincular-administrador-evento/', 'EventoController@vincularAdministradorEvento');
    Route::post('/desvincular-administrador-evento/', 'EventoController@desvincularAdministradorEvento');
    Route::get('/retornar-edicoes-evento-usuario/{user_id}', 'EventoController@retornarEdicoesEventoUsuario');
    Route::get('/retornar-evento/{evento_id}', 'EventoController@retornarEvento');
});
