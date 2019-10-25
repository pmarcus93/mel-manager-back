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
}
