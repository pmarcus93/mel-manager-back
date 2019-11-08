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

}
