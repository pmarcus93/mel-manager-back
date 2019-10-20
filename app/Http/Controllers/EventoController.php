<?php

namespace App\Http\Controllers;

use App\Evento;
use App\EventoAdministrador;
use App\Response\MelResponse;
use App\User;
use Illuminate\Support\Facades\DB;

class EventoController extends Controller
{
    public function cadastrarEvento() {
        try {
            $user_id = \request('user_id');
            $nome = \request('nome');

            $usuario = User::find($user_id);

            if (!$usuario) {
                throw new \Exception("Não existe usuário no banco de dados com o id informado [" . $user_id . "].");
            }

            DB::beginTransaction();

            $evento = new Evento();
            $evento->nome = $nome;
            $evento->save();

            $eventoAdministrador = new EventoAdministrador();
            $eventoAdministrador->evento_id = $evento->id;
            $eventoAdministrador->user_id = $user_id;
            $eventoAdministrador->save();

            DB::commit();

            return MelResponse::success("Evento cadastrado com sucesso!", $evento);

        } catch (\Exception $e) {
            DB::rollBack();
            return MelResponse::error("Erro ao cadastrar evento.", $e->getMessage());
        }
    }

    public function retornarAdministradores() {
        try {
            $evento_id = \request('id');
            $eventoAdministradores = EventoAdministrador::where('evento_id', $evento_id)->get();

            $usuarios = [];

            foreach ($eventoAdministradores as $eventoAdministrador) {
                $usuario = User::find($eventoAdministrador->user_id);
                $usuarios[] = ['id' => $usuario->id, 'name' => $usuario->name];
            }

            return MelResponse::success(null, $usuarios);
        } catch (Exception $e) {
            return MelResponse::error("Não foi possível retornar os administradores deste evento.", $e->getMessage());
        }
    }
}
