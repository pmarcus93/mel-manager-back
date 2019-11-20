<?php

namespace App\Business;

use App\Categoria;
use App\FluxoCaixa;
use App\EventoEdicao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class FluxoCaixaBusiness
{

    /**
     * Cadastra um novo lancaçamento no fluxo de caixa
     * @param Request $request
     * @return FluxoCaixa
     * @throws \Exception
     */
    public function cadastrarFluxoCaixa(Request $request)
    {
        $attributes = $request->validate([
            'eventoedicao_id' => 'required',
            'categoria_id' => 'required',
            'nome_operacao' => 'required',
            'valor' => 'required',
            'data_movimento' => 'required',
            'tipo_operacao' => ['required', Rule::in(['DEBITO', 'CREDITO'])]
        ]);

        /** @var EventoEdicao $edicaoEventoExistente */
        $edicaoEventoExistente = EventoEdicao::find($attributes['eventoedicao_id']);

        if (!$edicaoEventoExistente) {
            throw new \Exception("Não existe edição de evento cadastrado com o ID " . $attributes['eventoedicao_id'] . "!");
        }


        /** @var Categoria $categoriaExistente */
        $categoriaExistente = Categoria::find($attributes['categoria_id']);

        if (!$categoriaExistente) {
            throw new \Exception("Não existe categoria cadastrada com o ID " . $attributes['categoria_id'] . "!");
        }

        DB::beginTransaction();
        $fluxoCaixa = new FluxoCaixa();
        $fluxoCaixa->categoria_id = $attributes['categoria_id'];
        $fluxoCaixa->nome_operacao = $attributes['nome_operacao'];
        $fluxoCaixa->valor = Str::replaceFirst(',', '.', $attributes['valor']);
        $fluxoCaixa->data_movimento = $attributes['data_movimento'];
        $fluxoCaixa->tipo_operacao = $attributes['tipo_operacao'];
        $edicaoEventoExistente->fluxosCaixa()->save($fluxoCaixa);
        DB::commit();

        return $fluxoCaixa;
    }

    /**
     * Editar um lançamento no fluxo de caixa
     * @param Request $request
     * @return FluxoCaixa
     * @throws \Exception
     */
    public function editarFluxoCaixa(Request $request)
    {
        $attributes = $request->validate([
            'categoria_id' => 'present',
            'fluxocaixa_id' => 'required',
            'nome_operacao' => 'required',
            'valor' => ['required'],
            'data_movimento' => 'present',
            'tipo_operacao' => 'present'
        ]);

        /** @var FluxoCaixa $fluxoCaixa */
        $fluxoCaixa = FluxoCaixa::find($attributes['fluxocaixa_id']);

        if (!$fluxoCaixa) {
            throw new \Exception("Não existe fluxo de caixa cadastrado com o ID " . $attributes['fluxocaixa_id'] . "!");
        }

        if (!empty($attributes['categoria_id'])) {
            $categoriaExistente = Categoria::find($attributes['categoria_id']);

            if (!$categoriaExistente) {
                throw new \Exception("Não existe categoria cadastrada com o ID " . $attributes['categoria_id'] . "!");
            }
        }

        $fluxoCaixa->nome_operacao = $attributes['nome_operacao'];
        $fluxoCaixa->valor = Str::replaceFirst(',', '.', $attributes['valor']);

        if (!empty($attributes['tipo_operacao'])) {

            if (!in_array($attributes['tipo_operacao'], ['DEBITO', 'CREDITO'])) {
                throw new \Exception("Operação " . $attributes['tipo_operacao'] . " inválida. Utilize os tipos CREDITO ou DEBITO.");
            }
            $fluxoCaixa->tipo_operacao = $attributes['tipo_operacao'];
        }

        if (!empty($attributes['data_movimento'])) {
            $fluxoCaixa->data_movimento = $attributes['data_movimento'];
        }

        if (!empty($attributes['categoria_id'])) {
            $fluxoCaixa->categoria_id = $attributes['categoria_id'];
        }

        $fluxoCaixa->save();

        return $fluxoCaixa;
    }

    /**
     * Remover um lançamento no fluxo de caixa
     * @param Request $request
     * @return FluxoCaixa
     * @throws \Exception
     */
    public function removerFluxoCaixa(Request $request)
    {
        $attributes = $request->validate([
            'fluxoCaixa_id' => 'required'
        ]);

        /** @var FluxoCaixa $fluxoCaixa */
        $fluxoCaixa = FluxoCaixa::find($attributes['fluxoCaixa_id']);

        if (!$fluxoCaixa) {
            throw new \Exception("Não existe fluxo de caixa cadastrado com o ID " . $attributes['fluxoCaixa_id'] . "!");
        }

        $fluxoCaixa->delete();

        return $fluxoCaixa;
    }

    /**
     * Retorna um lançamento no fluxo de caixa
     * @param $fluxocaixa_id
     * @return FluxoCaixa
     * @throws \Exception
     */
    public function retornarFluxoCaixa($fluxocaixa_id)
    {
        /** @var FluxoCaixa $fluxoCaixa */
        $fluxoCaixa = FluxoCaixa::find($fluxocaixa_id);

        if (!$fluxoCaixa) {
            throw new \Exception("Não existe fluxo de caixa cadastrado com o ID " . $fluxocaixa_id . "!");
        }
        return $fluxoCaixa;
    }

    /**Retornar todos os lançamentos do fluxo de caixa com base na edição do evento
     * @param $edicaoEvento_id
     * @return mixed
     * @throws \Exception
     */
    public function retornarFluxosPorEdicaoEvento($edicaoEvento_id)
    {
        /** @var EventoEdicao $edicaoEvento */
        $edicaoEvento = EventoEdicao::find($edicaoEvento_id);

        if (!$edicaoEvento) {
            throw new \Exception("Não existe edição de evento cadastrado com o ID " . $edicaoEvento_id . "!");
        }

        $edicaoEvento->load('fluxosCaixa');

        return $edicaoEvento->fluxosCaixa;
    }
}
