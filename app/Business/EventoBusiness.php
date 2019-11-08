<?php

namespace App\Business;

use App\Evento;
use App\EventoEdicao;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventoBusiness
{
    /**
     * Cadastra uma novo evento.
     * @param Request $request
     * @return Evento
     * @throws \Exception
     */
    public function cadastrarEvento(Request $request)
    {
        $attributes = $request->validate([
            'user_id' => 'required',
            'nome' => 'nome'
        ]);

        /** @var User $usuario */
        $usuario = User::find($attributes['user_id']);

        if (!$usuario) {
            throw new \Exception("Não existe usuário no banco de dados com o id informado [" . $attributes['user_id'] . "].");
        }

        DB::beginTransaction();
        $evento = new Evento();
        $evento->nome = $attributes['nome'];
        $evento->save();
        $evento->administradores()->attach($usuario->id);
        DB::commit();

        return $evento;
    }

    public function editarEvento(Request $request)
    {
        $attributes = $request->validate([
            'user_id' => 'required',
            'nome' => 'nome'
        ]);

        $evento = Evento::find($attributes['evento_id']);

        if (!$evento) {
            throw new \Exception("Nenhum evento com o id " . $attributes['evento_id'] . " encontrado.");
        }

        $evento->nome = $attributes['nome'];
        $evento->save();

        return $evento;
    }

    public function retornarEvento($evento_id)
    {
        /** @var Evento $evento */
        $evento = Evento::find($evento_id);

        if (!$evento) {
            throw new \Exception( "Nenhum evento com o id " . $evento_id . " encontrado.");
        }

        return $evento;
    }

    public function retornarEdicoesEvento($evento_id)
    {
        $edicoes = EventoEdicao::where('evento_id', $evento_id)
            ->get();

        if (!$edicoes) {
            throw new \Exception( "Nenhuma edição de evento para o evento com com o id " . $evento_id . " encontrado.");
        }

        return $edicoes;
    }

    public function retornarEventosUsuario($user_id)
    {
        $evento = Evento::query()
            ->join('evento_administrador', 'evento_id', 'evento.id')
            ->where('evento_administrador.user_id', '=', $user_id)
            ->select('evento.*')->get();

        if (!$evento) {
            throw new \Exception( "Nenhum evento encontrado para o usuário informado.");
        }

        return $evento;
    }

    public function retornarEmpresaDeEvento(Request $request)
    {
        $attributes = $request->validate([
            'evento_id' => 'required'
        ]);

        $evento = Evento::find($attributes['evento_id']);
        $evento->load('empresas');
        $empresas = $evento->empresas;

        return $empresas;
    }

}
