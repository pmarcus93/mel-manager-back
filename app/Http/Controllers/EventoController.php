<?php

namespace App\Http\Controllers;

use App\Evento;
use App\EventoAdministrador;
use App\Response\MelResponse;
use Illuminate\Support\Facades\DB;
use App\Usuario;

class EventoController extends Controller
{
    public function cadastrarEvento() {

        try {

        $usuario_id = \request('usuario_id');
        $nome = \request('nome');

        $usuario = Usuario::find($usuario_id);

        if (!$usuario) {
            throw new \Exception("Não existe usuário no banco de dados com o id informado [" . $usuario_id . "].");
        }

        DB::beginTransaction();

        $evento = new Evento();
        $evento->nome = $nome;
        $evento->save();

        $eventoAdministrador = new EventoAdministrador();
        $eventoAdministrador->evento_id = $evento->id;
        $eventoAdministrador->usuario_id = $usuario_id;
        $eventoAdministrador->save();

        DB::commit();

        return MelResponse::success("Evento cadastrado com sucesso!", $evento);

        } catch (\Exception $e) {
            DB::rollBack();
            return MelResponse::error("Erro ao cadastrar evento.", $e->getMessage());
        }
    }
}
