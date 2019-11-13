<?php


namespace App\Business;


use App\Evento;
use App\Response\MelResponse;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UsuariosEventoBusiness
{

    /**
     * Retorna os administradores vinculados a um evento
     * @param $evento_id
     * @return Evento
     * @throws \Exception
     */
    public function retornarAdministradoresEvento($evento_id)
    {
        /** @var Evento $evento */
        $evento = Evento::find($evento_id);

        if (!$evento) {
            throw new \Exception("Nenhum evento encontrado.");
        }

        $evento->load('administradores');
        $administradores = $evento->administradores;
        return $administradores;
    }

    /**
     * Vincula um usuário como administrador de um evento.
     * @param Request $request
     * @return User
     * @throws \Exception
     */
    public function vincularAdministradorEvento(Request $request)
    {
        $attributes = $request->validate([
            'evento_id' => 'required',
            'user_id' => 'required'
        ]);

        /** @var Evento $evento */
        $evento = Evento::find($attributes['evento_id']);

        if (!$evento) {
            throw new \Exception("Nenhum evento encontrado com o id informado.");
        }

        /** @var User $eventoAdministrador */
        $eventoAdministrador = User::find($attributes['user_id']);

        if (!$eventoAdministrador) {
            throw new \Exception("Nenhum usuário encontrado com o id informado.");
        }

        $usuarioExistente = $evento->administradores()->find($eventoAdministrador->id);

        if ($usuarioExistente) {
            throw new \Exception("Administrador já vinculado ao evento.");
        }

        $evento->administradores()->attach($eventoAdministrador->id);
        return $eventoAdministrador;
    }

    /**
     * Desvincula um usuário como administrador de um evento.
     * @param Request $request
     * @return User
     * @throws \Exception
     */
    public function desvincularAdministradorEvento(Request $request)
    {
        $attributes = $request->validate([
            'user_id' => 'required',
            'evento_id' => 'required',
        ]);

        /** @var Evento $evento */
        $evento = Evento::find($attributes['evento_id']);

        if (!$evento) {
            throw new \Exception("Nenhum evendo encontrado com o id informado!");
        }

        /** @var User $eventoAdministrador */
        $eventoAdministrador = User::find($attributes['user_id']);

        if (!$eventoAdministrador) {
            throw new \Exception("Nenhum usuário encontrado com o id informado!");
        }

        $usuarioExistente = $evento->administradores()->find($eventoAdministrador->id);

        if (!$usuarioExistente) {
            throw new \Exception("Administrador não vinculado ao evento!");
        }

        $evento->administradores()->detach($eventoAdministrador->id);
        return $eventoAdministrador;
    }

}
