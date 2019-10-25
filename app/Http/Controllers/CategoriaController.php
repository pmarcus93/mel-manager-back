<?php

namespace App\Http\Controllers;

use Exception;
use App\Categoria;
use App\Response\MelResponse;

class CategoriaController extends Controller
{
    public function cadastrarCategoria()
    {
        try {
            $categoria_nome = request('nome');
            $categoria = new Categoria();
            $categoria->nome = $categoria_nome;
            $categoria->save();
            return MelResponse::success("Categoria cadastrada com sucesso!", $categoria);
        } catch (Exception $e) {
            return MelResponse::error("Erro ao cadastrar categoria.", $e->getMessage());
        }
    }

    public function editarCategoria()
    {
        try {
            $categoria_id = request('id');
            $categoria_nome = request('nome');
            $categoria = Categoria::find($categoria_id);

            if (!$categoria) {
                throw new \Exception("Categoria não econtrada para edição!");
            }

            $categoria->nome = $categoria_nome;
            $categoria->save();

            return MelResponse::success("Categoria alterada com sucesso!", $categoria);
        } catch (Exception $e) {
            return MelResponse::error("Erro ao alterar categoria.", $e->getMessage());
        }
    }
}
