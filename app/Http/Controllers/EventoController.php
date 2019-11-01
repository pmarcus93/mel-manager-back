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

            $evento->administrador()->attach($usuario->id);

            DB::commit();

            $evento = $evento->load('administrador');

            return MelResponse::success("Evento cadastrado com sucesso!", $evento);

        } catch (\Exception $e) {
            DB::rollBack();
            return MelResponse::error($e->getMessage());
        }
    }

    public function editarEvento()
    {
        try {
            $evento_id = request('evento_id');
            $nome = request('nome');

            $evento = Evento::find($evento_id);

            if (!$evento) {
                throw new \Exception("Nenhum evento com o id " . $evento_id . " encontrado.");
            }

            if (!$nome) {
                throw new \Exception("A descrição do evento deve ser informada.");
            }

            $evento->nome = $nome;
            $evento->save();

            return MelResponse::success('Evento alterado com sucesso.', $evento);
        } catch (Exception $e) {
            return MelResponse::error("Não foi possível editar os dados do evento.", $e->getMessage());
        }
    }

    public function retornarAdministradores()
    {
        try {
            $evento_id = \request('id');

            if (!$evento_id){
                throw new \Exception("É necessário informar o id.");
            }

            $evento = Evento::find($evento_id);

            if (!$evento){
                throw new \Exception("Nenhum evento encontrado.");
            }

            $evento->load('administrador');

            $users = $evento->administrador;

            return MelResponse::success(null, $users);
        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
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

            DB::beginTransaction();

            $eventoEdicao = new EventoEdicao();
            $eventoEdicao->nome = $nome;
            $eventoExistente->edicoes()->save($eventoEdicao);

            DB::commit();

            return MelResponse::success("Edição de evento cadastrada com sucesso!", $eventoEdicao);
        } catch (\Exception $e) {
            DB::rollBack();
            return MelResponse::error($e->getMessage());
        }
    }

    public function editarEdicaoEvento()
    {
        try {
            $edicao_id = request('edicao_id');
            $nome = request('nome');

            $edicao = EventoEdicao::find($edicao_id);

            if (!$edicao) {
                throw new Exception("Nenhuma edição de evento com o id " . $edicao_id . " encontrado.");
            }

            if (!$nome) {
                throw new Exception("A descrição da edição do evento deve ser informada.");
            }

            $edicao->nome = $nome;
            $edicao->save();

            return MelResponse::success('Edição de evento alterado com sucesso.', $edicao);
        } catch (Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

    public function removerEdicaoEvento()
    {
        try {
            $edicao_id = request('edicao_id');

            $edicao = EventoEdicao::find($edicao_id);

            if (!$edicao) {
                throw new Exception("Nenhuma edição de evento com o id " . $edicao_id . " encontrado.");
            }

            $edicao->ativo = 0;
            $edicao->save();

            return MelResponse::success(null, $edicao);
        } catch (Exception $e) {
            return MelResponse::error($e->getMessage());
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
            return MelResponse::error($e->getMessage());
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
            return MelResponse::error($e->getMessage());
        }
    }

    public function retornarEdicaoEvento()
    {
        try {
            $edicao_id = request("edicao_id");

            if (!$edicao_id) {
                throw new Exception("É necessário informar o id da edição.");
            }

            $edicao = EventoEdicao::find($edicao_id);

            if ($edicao->ativo === 0) {
                throw new Exception("Evento informado foi removido do sistema.");
            }

            return MelResponse::success(null, $edicao);
        } catch (Exception $e) {
            return MelResponse::error($e->getMessage());
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
            return MelResponse::error($e->getMessage());
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
            return MelResponse::error($e->getMessage());
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
            return MelResponse::error($e->getMessage());
        }
    }

}
