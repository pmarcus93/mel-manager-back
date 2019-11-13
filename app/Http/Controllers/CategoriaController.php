<?php

namespace App\Http\Controllers;

use App\Response\MelResponse;
use Illuminate\Http\Request;
use App\Business\CategoriaBusiness;
use Illuminate\Validation\ValidationException;

class CategoriaController extends Controller
{

    /**var CategoriaBusiness*/
    private $categoriaBusiness;

    public function __construct()
    {
        $this->categoriaBusiness = new CategoriaBusiness();
    }

    public function cadastrarCategoria(Request $request)
    {
        try {
            $categoria = $this->categoriaBusiness->cadastrarCategoria($request);
            return MelResponse::success("Categoria cadastrada com sucesso.", $categoria);
        } catch (ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

    public function editarCategoria(Request $request)
    {
        try {
            $categoria = $this->categoriaBusiness->editarCategoria($request);
            return MelResponse::success("Categoria atualizada com sucesso.", $categoria);
        } catch (ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

    public function removerCategoria(Request $request)
    {
        try {
            $categoria = $this->categoriaBusiness->removerCategoria($request);
            return MelResponse::success("Categoria removida com sucesso.", $categoria);
        } catch (ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

    public function retornarCategoria($categoria_id)
    {
        try {
            $categoria = $this->categoriaBusiness->retornarCategoria($categoria_id);
            return MelResponse::success("Categoria encontrada.", $categoria);
        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

    public function retornarCategorias()
    {
        try {
            $categorias = $this->categoriaBusiness->retornarCategorias();
            return MelResponse::success("Categorias encontradas.", $categorias);
        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }
}
