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
    public function cadastrarFluxoCaixa(Request $request)
    {
        $attributes = $request->validate([
            'eventoedicao_id' => 'required',
            'categoria_id' => 'required',
            'nome_operacao' => 'required',
            'valor' => 'required',
            'tipo_operacao' => ['required', Rule::in(['DEBITO', 'CREDITO'])]
        ]);

        $edicaoEventoExistente = EventoEdicao::find($attributes['eventoedicao_id']);

        if (!$edicaoEventoExistente) {
            throw new \Exception("Não existe edição de evento cadastrado com o ID " . $attributes['eventoedicao_id'] . "!");
        }

        $categoriaExistente = Categoria::find($attributes['categoria_id']);

        if (!$categoriaExistente) {
            throw new \Exception("Não existe categoria cadastrada com o ID " . $attributes['categoria_id'] . "!");
        }

        DB::beginTransaction();
        $fluxoCaixa = new FluxoCaixa();
        $fluxoCaixa->categoria_id = $attributes['categoria_id'];
        $fluxoCaixa->nome_operacao = $attributes['nome_operacao'];
        $fluxoCaixa->valor = $attributes['valor'];
        $fluxoCaixa->tipo_operacao = $attributes['tipo_operacao'];
        $edicaoEventoExistente->fluxosCaixa()->save($fluxoCaixa);
        DB::commit();

        return $fluxoCaixa;
    }

    public function editarFluxoCaixa(Request $request)
    {
        $attributes = $request->validate([
            'categoria_id' => 'present',
            'fluxoCaixa_id' => 'required',
            'nome_operacao' => 'required',
            'valor' => ['required'],
            'tipo_operacao' => 'present'
        ]);

        $fluxoCaixa = FluxoCaixa::find($attributes['fluxoCaixa_id']);

        if (!$fluxoCaixa) {
            throw new \Exception("Não existe fluxo de caixa cadastrado com o ID " . $attributes['fluxoCaixa_id'] . "!");
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
                throw new \Exception("Tipo de operação " . $attributes['tipo_operacao'] . " não é válida, use os tipos CREDITO OU DEBITO!");
            }
            $fluxoCaixa->tipo_operacao = $attributes['tipo_operacao'];
        }

        if (!empty($attributes['categoria_id'])) {
            $fluxoCaixa->categoria_id = $attributes['categoria_id'];
        }

        $fluxoCaixa->save();

        return $fluxoCaixa;
    }
}
