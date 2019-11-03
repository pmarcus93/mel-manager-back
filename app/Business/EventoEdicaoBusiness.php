<?php


namespace App\Business;


use App\Evento;
use App\EventoEdicao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventoEdicaoBusiness
{

    /**
     * Retorna um registro da tabela eventoedicao com base em seu id.
     * @param $eventoedicao_id
     * @return EventoEdicao
     * @throws \Exception
     */
    public function retornarEdicaoEvento($eventoedicao_id)
    {
        /** @var EventoEdicao $eventoedicao */
        $eventoedicao = EventoEdicao::find($eventoedicao_id);
        if ($eventoedicao->ativo === 0) {
            throw new \Exception("Evento informado foi removido do sistema.");
        }
        return $eventoedicao;
    }

    /**
     * Cadastra uma nova edição de evento.
     * @param Request $request
     * @return EventoEdicao
     * @throws \Exception
     */
    public function cadastrarEventoEdicao(Request $request)
    {

        $attributes = $request->vaidate([
            'evento_id' => 'required',
            'nome' => 'required'
        ]);

        /** @var Evento $eventoExistente */
        $eventoExistente = Evento::find($attributes['evento_id']);

        if (!$eventoExistente) {
            throw new \Exception("Não existe evento cadastrado com o ID " . $attributes['evento_id'] . "!");
        }

        DB::beginTransaction();
        $eventoEdicao = new EventoEdicao();
        $eventoEdicao->nome = $attributes['nome'];
        $eventoExistente->edicoes()->save($eventoEdicao);
        DB::commit();

        return $eventoEdicao;
    }

    /**
     * Edita uma edição de evento.
     * @param Request $request
     * @return EventoEdicao
     * @throws \Exception
     */
    public function editarEventoEdicao(Request $request)
    {
        $attributes = $request->validate([
            'edicao_id' => 'required',
            'nome' => 'required',
        ]);

        /** @var EventoEdicao $eventoEdicao */
        $eventoEdicao = EventoEdicao::find($attributes['edicao_id']);

        if (!$eventoEdicao) {
            throw new \Exception("Nenhuma edição de evento com o id " . $attributes['edicao_id'] . " encontrado.");
        }

        $eventoEdicao->nome = $attributes['nome'];
        $eventoEdicao->save();
        return $eventoEdicao;
    }


    /**
     * Remove (inativa) uma edição de evento.
     * TODO: ajustar após implementar soft delete global.
     * @param Request $request
     * @return EventoEdicao
     * @throws \Exception
     */
    public function removerEdicaoEvento(Request $request)
    {
        $attributes = $request->validate([
            'edicao_id' => 'required'
        ]);

        /** @var EventoEdicao $edicao */
        $edicao = EventoEdicao::find($attributes['edicao_id']);

        if (!$edicao) {
            throw new \Exception("Nenhuma edição de evento com o id " . $attributes['edicao_id'] . " encontrado.");
        }

        $edicao->ativo = 0;
        $edicao->save();
        return $edicao;
    }

}
