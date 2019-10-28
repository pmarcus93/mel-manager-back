<?php

namespace App\Http\Controllers;

use App\Evento;
use App\EventoAdministrador;
use App\EventoEdicao;
use App\Response\MelResponse;
use App\User;
use Illuminate\Support\Facades\DB;

class EventoController extends Controller
{
    public function cadastrarEvento()
    {
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

            $evento->attach($usuario->id);

            DB::commit();

            return MelResponse::success("Evento cadastrado com sucesso!", $evento);

        } catch (\Exception $e) {
            DB::rollBack();
            return MelResponse::error("Erro ao cadastrar evento.", $e->getMessage());
        }
    }

    public function retornarAdministradores()
    {
        try {
            $evento_id = \request('id');
            $eventoAdministradores = EventoAdministrador::where('evento_id', $evento_id)->get();
            $users = [];

            foreach ($eventoAdministradores as $eventoAdministrador) {
                $usuario = User::find($eventoAdministrador->user_id);
                $users[] = ['id' => $usuario->id, 'name' => $usuario->name];
            }
            return MelResponse::success(null, $users);
        } catch (\Exception $e) {
            return MelResponse::error("Não foi possível retornar os administradores deste evento.", $e->getMessage());
        }
    }

    public function cadastrarEdicaoEvento()
    {
        try {
            $evento_id = request('evento_id');
            $nome = request('nome');

            if (!$evento_id || !$nome) {
                throw new \Exception("É necessário informar o id do evento e o nome da edição!");
            }

            $eventoExistente = Evento::find($evento_id);

            if (!$eventoExistente) {
                throw new \Exception("Não existe evento cadastrado com o ID " . $evento_id . "!");
            }

            $eventoEdicao = new EventoEdicao();
            $eventoEdicao->nome = $nome;
            $eventoEdicao->evento_id = $evento_id;

            $eventoEdicao->save();

            return MelResponse::success("Edição de evento cadastrada com sucesso!", $eventoEdicao);
        } catch (\Exception $e) {
            return MelResponse::error("Não foi possível cadastrar a edição do evento.", $e->getMessage());
        }
    }

    public function retornarEvento()
    {
        try {
            $msg = "Informações do evento obtidas com sucesso.";
            $evento_id = request('evento_id');

            /** @var Evento $evento */
            $evento = Evento::find($evento_id);

            if (!$evento) {
                $msg = "Nenhum evento com o id " . $evento_id . " encontrado.";
            }

            return MelResponse::success($msg, $evento);
        } catch (\Exception $e) {
            return MelResponse::error("Não foi possível retornar os dados do evento.", $e->getMessage());
        }
    }

    public function retornarEdicoesEvento()
    {
        try {
            $evento_id = request("id");

            if (!$evento_id) {
                throw new \Exception("É necessário informar o id do evento.");
            }

            $edicoes = EventoEdicao::where('evento_id', $evento_id)->get();
            return MelResponse::success("", $edicoes);
        } catch (\Exception $e) {
            return MelResponse::error("Não foi possível retornar as edições do evento.", $e->getMessage());
        }
    }

    public function retornarEdicoesEventoUsuario()
    {
        try {
            $user_id = request('user_id');

            $eventosUsuario = DB::table('evento_administrador')
                ->join('users', 'evento_administrador.user_id', '=', 'users.id')
                ->join('evento', 'evento_administrador.evento_id', '=', 'evento.id')
                ->where('users.id', '=', $user_id)
                ->select('users.id AS user_id', 'users.name', 'evento.id AS evento_id', 'evento.nome')
                ->get();

            foreach ($eventosUsuario as &$evento) {
                $eventoEdicoes = EventoEdicao::select('id AS edicao_id', 'nome')
                    ->where('evento_id', $evento->evento_id)
                    ->get();
                $evento->edicoes = $eventoEdicoes;
            }

            return MelResponse::success("", $eventosUsuario);
        } catch (\Exception $e) {
            return MelResponse::error("Não foi possível retornar os eventos do usuário.", $e->getMessage());
        }
    }

    public function vincularAdministradorEvento()
    {
        try {
            $evento_id = request('evento_id');
            $user_id = request('user_id');

            $eventoAdministrador = EventoAdministrador::where('evento_id', $evento_id)
                ->where('user_id', $user_id)
                ->first();

            if ($eventoAdministrador && $eventoAdministrador->ativo) {
                throw new \Exception("Este usuário já é administrador deste evento!");
            }

            if (!$eventoAdministrador) {
                $eventoAdministrador = new EventoAdministrador();
                $eventoAdministrador->user_id = $user_id;
                $eventoAdministrador->evento_id = $evento_id;
            }

            $eventoAdministrador->ativo = 1;
            $eventoAdministrador->save();
            return MelResponse::success("Administrador vinculado com sucesso.", $eventoAdministrador);
        } catch (\Exception $e) {
            return MelResponse::error("Não foi possível vincular esse usuário como administrador deste evento.", $e->getMessage());
        }
    }

    public function desvincularAdministradorEvento()
    {
        try {
            $user_id = request('user_id');
            $evento_id = request('evento_id');

            /** @var EventoAdministrador $eventoAdministrador */
            $eventoAdministrador = EventoAdministrador::where('evento_id', $evento_id)
                ->where('user_id', $user_id)
                ->first();

            if (!$eventoAdministrador || $eventoAdministrador->ativo === 0) {
                throw new \Exception("O usuário não está vinculado como administrador para o evento.");
            };

            $eventoAdministrador->ativo = 0;
            $eventoAdministrador->save();
            return MelResponse::success("Administrador desvinculado com sucesso.", $eventoAdministrador);
        } catch (\Exception $e) {
            return MelResponse::error("Não foi possível desvincular o administrador do evento.", $e->getMessage());
        }
    }

}
