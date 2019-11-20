<?php

namespace App\Business;

use App\Empresa;
use Exception;
use Illuminate\Http\Request;

class EmpresaBusiness
{

    /**
     * Cadastra uma empresa.
     * @param Request $request
     * @return Empresa
     */
    public function cadastrarEmpresa(Request $request)
    {
        $attributes = $request->validate([
            'nome' => 'required',
            'telefone' => 'required',
            'evento_id' => 'required'
        ]);

        /** @var Empresa $empresa */
        $empresa = new Empresa();
        $empresa->nome = $attributes['nome'];
        $empresa->telefone = $attributes['telefone'];
        $empresa->evento_id = $attributes['evento_id'];

        $empresa->save();
        return $empresa;
    }


    /**
     * Edita os dados de uma empresa.
     * @param Request $request
     * @return mixed
     * @throws Exception
     */
    public function editarEmpresa(Request $request)
    {
        $attributes = $request->validate([
            'id' => 'required',
            'nome' => 'required',
            'telefone' => 'required'
        ]);

        /** @var Empresa $empresa */
        $empresa = Empresa::find($attributes['id']);

        if (!$empresa) {
            throw new Exception("Não existe empresa cadastrada com o ID " . $attributes['id'] . "!");
        }

        $empresa->nome = $attributes['nome'];
        $empresa->telefone = $attributes['telefone'];
        $empresa->save();

        return $empresa;
    }

    /**
     * Deleta a empresa.
     * @param Request $request
     * @return Empresa
     * @throws Exception
     */
    public function removerEmpresa(Request $request)
    {
        $attributes = $request->validate([
            'empresa_id' => 'required'
        ]);

        /** @var Empresa $empresa */
        $empresa = Empresa::find($attributes['empresa_id']);

        if (!$empresa) {
            throw new Exception("Não existe empresa cadastrada com o ID " . $attributes['empresa_id'] . "!");
        }

        $empresa->delete();

        return $empresa;
    }

    /**
     * Retorna os dados de uma empresa.
     * @param $empresa_id
     * @return Empresa
     * @throws Exception
     */
    public function retornarEmpresa($empresa_id)
    {
        /** @var Empresa $empresa */
        $empresa = Empresa::find($empresa_id);

        if (!$empresa) {
            throw new Exception("Não existe empresa cadastrada com o ID " . $empresa_id . "!");
        }

        return $empresa;
    }

    /**
     * Retorna empresas de um evento
     * @param $evento_id
     * @return Empresa
     * @throws Exception
     */
    public function retornarEmpresasEvento($evento_id)
    {
        /** @var Empresa $empresa */
        $empresa = Empresa::where('evento_id', $evento_id)->get();

        return $empresa;
    }

}
