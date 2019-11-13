<?php

namespace App\Business;

use App\Categoria;
use Illuminate\Http\Request;

class CategoriaBusiness
{
    /**
     * @param Request $request
     * @return Categoria
     */
    public function cadastrarCategoria(Request $request)
    {
        $attributes = $request->validate([
            'nome' => 'required'
        ]);

        $categoria = new Categoria();
        $categoria->nome = $attributes['nome'];
        $categoria->save();

        return $categoria;
    }

}
