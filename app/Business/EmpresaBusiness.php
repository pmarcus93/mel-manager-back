<?php

namespace App\Business;

use App\Empresa;
use App\Evento;
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
            'telefone' => 'required'
        ]);

        /** @var Empresa $empresa */
        $empresa = new Empresa();
        $empresa->nome = $attributes['nome'];
        $empresa->telefone = $attributes['telefone'];

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
            'empresa_id' => 'required',
            'empresa_nome' => 'required',
            'telefone' => 'required'
        ]);

        /** @var Empresa $empresa */
        $empresa = Empresa::find($attributes['empresa_id']);

        if (!$empresa) {
            throw new Exception("Não existe empresa cadastrada com o ID " . $attributes['empresa_id'] . "!");
        }

        $empresa->nome = $attributes['empresa_nome'];
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
     * Vincular um evento a uma empresa.
     * @param Request $request
     * @return Evento
     * @throws Exception
     */
    public function vincularEventoEmpresa(Request $request)
    {
        $attributes = $request->validate([
            'empresa_id' => 'required',
            'evento_id' => 'required'
        ]);

        /** @var Empresa $empresa */
        $empresa = Empresa::find($attributes['empresa_id']);

        /** @var Evento $evento */
        $evento = Evento::find($attributes['evento_id']);

        if (!$empresa) {
            throw new Exception("Não existe empresa cadastrada com o ID " . $attributes['empresa_id'] . "!");
        }

        if (!$evento) {
            throw new Exception("Não existe evento cadastrado com o ID " . $attributes['evento_id'] . "!");
        }

        $empresa->eventos()->attach($attributes['evento_id']);

        /** @var Evento $eventos */
        $eventos = $empresa->load('eventos');

        return $eventos;
    }

    /**
     * Desvincula evento da empresa.
     * @param Request $request
     * @return Evento
     * @throws Exception
     */
    public function desvincularEventoEmpresa(Request $request)
    {
        $attributes = $request->validate([
            'empresa_id' => 'required',
            'evento_id' => 'required'
        ]);

        /** @var Empresa $empresa */
        $empresa = Empresa::find($attributes['empresa_id']);

        /** @var Evento $evento */
        $evento = Evento::find($attributes['evento_id']);

        if (!$empresa) {
            throw new Exception("Não existe empresa cadastrada com o ID " . $attributes['empresa_id'] . "!");
        }

        if (!$evento) {
            throw new Exception("Não existe evento cadastrado com o ID " . $attributes['evento_id'] . "!");
        }

        $empresa->eventos()->detach($attributes['evento_id']);

        /** @var Evento $eventos */
        $eventos = $empresa->load('eventos');

        return $eventos;
    }
}
