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

    /**
     * @param Request $request
     * @return Categoria
     * @throws \Exception
     */
    public function editarCategoria(Request $request)
    {
        $attributes = $request->validate([
            'categoria_id' => 'required',
            'nome' => 'required'
        ]);

        /** @var Categoria $categoria */
        $categoria = Categoria::find($attributes['categoria_id']);

        if(!$categoria){
            throw new \Exception("Não existe categoria cadastrada com o ID " . $attributes['categoria_id'] . "!");
        }

        $categoria->nome = $attributes['nome'];
        $categoria->save();

        return $categoria;
    }

    /**
     * @param Request $request
     * @return Categoria
     * @throws \Exception
     */
    public function removerCategoria(Request $request)
    {
        $attributes = $request->validate([
            'categoria_id' => 'required'
        ]);

        /** @var Categoria $categoria */
        $categoria = Categoria::find($attributes['categoria_id']);

        if(!$categoria){
            throw new \Exception("Não existe categoria cadastrada com o ID " . $attributes['categoria_id'] . "!");
        }

        $categoria->delete();

        return $categoria;
    }

}
