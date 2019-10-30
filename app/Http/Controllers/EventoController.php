<?php

namespace App\Http\Controllers;

use Exception;
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
            $user_id = request('user_id');
            $nome = request('nome');

            $usuario = User::find($user_id);

            if (!$usuario) {
                throw new Exception("Não existe usuário no banco de dados com o id informado [" . $user_id . "].");
            }

            DB::beginTransaction();

            $evento = new Evento();
            $evento->nome = $nome;
            $evento->save();

            $evento->administrador()->attach($usuario->id);

            DB::commit();

            $evento = $evento->load('administrador');

            return MelResponse::success("Evento cadastrado com sucesso!", $evento);

        } catch (Exception $e) {
            DB::rollBack();
            return MelResponse::error("Erro ao cadastrar evento.", $e->getMessage());
        }
    }

    public function editarEvento()
    {
        try {
            $evento_id = request('evento_id');
            $nome = request('nome');

            $evento = Evento::find($evento_id);

            if (!$evento) {
                throw new Exception("Nenhum evento com o id " . $evento_id . " encontrado.");
            }

            if (!$nome) {
                throw new Exception("A descrição do evento deve ser informada.");
            }

            $evento->nome = $nome;
            $evento->save();

            return MelResponse::success('Evento alterado com sucesso.', $evento);
        } catch (Exception $e) {
            return MelResponse::error("Não foi possível editar os dados do evento.", $e->getMessage());
        }
    }

    public function cadastrarEdicaoEvento()
    {
        try {
            $evento_id = request('evento_id');
            $nome = request('nome');

            if (!$evento_id || !$nome) {
                throw new Exception("É necessário informar o id do evento e o nome da edição!");
            }

            $evento = Evento::find($evento_id);

            if (!$evento) {
                throw new Exception("Não existe evento cadastrado com o ID " . $evento_id . "!");
            }

            DB::beginTransaction();

            $eventoEdicao = new EventoEdicao();
            $eventoEdicao->nome = $nome;
            $evento->edicoes()->save($eventoEdicao);

            DB::commit();

            $evento = $evento->load('administrador');

            return MelResponse::success("Edição de evento cadastrada com sucesso!", $evento);
        } catch (Exception $e) {
            DB::rollBack();
            return MelResponse::error("Não foi possível cadastrar a edição do evento.", $e->getMessage());
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
            return MelResponse::error("Não foi possível editar os dados da edição do evento.", $e->getMessage());
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

            return MelResponse::success('Edição de evento removida com sucesso.', $edicao);
        } catch (Exception $e) {
            return MelResponse::error("Não foi possível remover os dados da edição do evento.", $e->getMessage());
        }
    }

    public function retornarAdministradores()
    {
        try {
            $evento_id = request('id');

            if (!$evento_id) {
                throw new Exception("É necessário informar o id.");
            }

            $evento = Evento::find($evento_id);

            if (!$evento) {
                throw new Exception("Nenhum evento encontrado.");
            }

            $evento = $evento->load('administrador');

            return MelResponse::success(null, $evento);
        } catch (Exception $e) {
            return MelResponse::error("Não foi possível retornar os administradores deste evento.", $e->getMessage());
        }
    }


    public function retornarEvento()
    {
        try {
            $msg = "Informações do evento obtidas com sucesso.";
            $evento_id = request('evento_id');

            $evento = Evento::find($evento_id);

            if (!$evento) {
                $msg = "Nenhum evento com o id " . $evento_id . " encontrado.";
            }

            $evento = $evento->load('administrador');

            $evento = $evento->load([
                'edicoes' => function ($query) {
                    $query->where('ativo', 1);
                }
            ]);

            return MelResponse::success($msg, $evento);
        } catch (Exception $e) {
            return MelResponse::error("Não foi possível retornar os dados do evento.", $e->getMessage());
        }
    }

    public function retornarEdicaoEventoPorId()
    {
        try {
            $evento_id = request("evento_id");
            $edicao_id = request("edicao_id");

            if (!$evento_id) {
                throw new Exception("É necessário informar o id do evento.");
            }

            if (!$edicao_id) {
                throw new Exception("É necessário informar o id da edição.");
            }

            $evento = Evento::find($evento_id);

            if (!$evento) {
                throw new Exception("Nenhum evendo encontrado para o valor informado!");
            }

            $evento = $evento->load('edicoes');
            $edicao = $evento->edicoes()->find($edicao_id);

            if ($edicao->ativo === 0) {
                throw new Exception("Evento informado foi removido do sistema.");
            }

            return MelResponse::success(null, $edicao);
        } catch (Exception $e) {
            return MelResponse::error("Não foi possível retornar as edições do evento.", $e->getMessage());
        }
    }

    public function vincularAdministradorEvento()
    {
        try {
            $evento_id = request('evento_id');
            $user_id = request('user_id');

            $evento = Evento::find($evento_id);

            if (!$evento) {
                throw new Exception("Nenhum evendo encontrado com o id informado!");
            }

            $usuario = User::find($user_id);

            if (!$usuario) {
                throw new Exception("Nenhum usuário encontrado com o id informado!");
            }

            $usuarioExistente = $evento->administrador()->find($usuario->id);

            if ($usuarioExistente) {
                throw new Exception("Administrador já vinculado ao evento!");
            }

            $evento->administrador()->attach($usuario->id);

            $evento = $evento->load('administrador');

            return MelResponse::success("Administrador vinculado com sucesso.", $evento);
        } catch (Exception $e) {
            return MelResponse::error("Não foi possível vincular esse usuário como administrador deste evento.", $e->getMessage());
        }
    }

    public function desvincularAdministradorEvento()
    {
        try {
            $evento_id = request('evento_id');
            $user_id = request('user_id');

            $evento = Evento::find($evento_id);

            if (!$evento) {
                throw new Exception("Nenhum evendo encontrado com o id informado!");
            }

            $usuario = User::find($user_id);

            if (!$usuario) {
                throw new Exception("Nenhum usuário encontrado com o id informado!");
            }

            $usuarioExistente = $evento->administrador()->find($usuario->id);

            if (!$usuarioExistente) {
                throw new Exception("Administrador não vinculado ao evento!");
            }

            $evento->administrador()->detach($usuario->id);

            $evento = $evento->load('administrador');

            return MelResponse::success("Administrador desvinculado com sucesso.", $evento);
        } catch (Exception $e) {
            return MelResponse::error("Não foi possível desvincular esse usuário deste evento.", $e->getMessage());
        }
    }

    public function retornarEdicoesEventoUsuario()
    {
        try {
            $user_id = request('user_id');

            $collection = Evento::all();

            dd($collection);

            $evento = $collection->filter(function ($value,$key) use ($user_id) {
                if ($value['user_id'] == $user_id) {
                    return true;
                }
            });

            $evento->all();

            $evento = $evento->load('administrador');

            $evento = $evento->load('edicoes');

            return MelResponse::success("", $evento);
        } catch (Exception $e) {
            return MelResponse::error("Não foi possível retornar os eventos do usuário.", $e->getMessage());
        }
    }

}
