<?php

namespace App\Http\Controllers;

use App\Evento;
use App\Response\MelResponse;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UsuariosEventoController extends Controller
{
    public function retornarAdministradoresEvento($evento_id)
    {
        try {
            $evento = Evento::find($evento_id);

            if (!$evento) {
                throw new \Exception("Nenhum evento encontrado.");
            }

            $evento->load('administradores');
            $users = $evento->administradores;
            return MelResponse::success(null, $users);
        } catch (ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

    public function vincularAdministradorEvento(Request $request)
    {
        try {

            $attributes = $request->required([
                'evento_id' => 'required',
                'user_id' => 'required'
            ]);

            $evento = Evento::find($attributes['evento_id']);

            if (!$evento) {
                throw new \Exception("Nenhum evento encontrado com o id informado!");
            }

            $eventoAdministrador = User::find($attributes['user_id']);

            if (!$eventoAdministrador) {
                throw new \Exception("Nenhum usuário encontrado com o id informado!");
            }

            $usuarioExistente = $evento->administradores()->find($eventoAdministrador->id);

            if ($usuarioExistente) {
                throw new \Exception("Administrador já vinculado ao evento!");
            }

            $evento->administradores()->attach($eventoAdministrador->id);

            return MelResponse::success("Administrador vinculado com sucesso.", $eventoAdministrador);
        } catch (ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

    public function desvincularAdministradorEvento(Request $request)
    {
        try {

            $attributes = $request->validate([
                'user_id' => 'required',
                'evento_id' => 'required',
            ]);

            $evento = Evento::find($attributes['evento_id']);

            if (!$evento) {
                throw new \Exception("Nenhum evendo encontrado com o id informado!");
            }

            $eventoAdministrador = User::find($attributes['user_id']);

            if (!$eventoAdministrador) {
                throw new \Exception("Nenhum usuário encontrado com o id informado!");
            }

            $usuarioExistente = $evento->administradores()->find($eventoAdministrador->id);

            if (!$usuarioExistente) {
                throw new \Exception("Administrador não vinculado ao evento!");
            }

            $evento->administradores()->detach($eventoAdministrador->id);

            return MelResponse::success("Administrador desvinculado com sucesso.", $eventoAdministrador);
        } catch (ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

}
