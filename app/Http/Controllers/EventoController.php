<?php

namespace App\Http\Controllers;

use App\Evento;
use App\EventoEdicao;
use App\Response\MelResponse;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EventoController extends Controller
{
    public function cadastrarEvento(Request $request)
    {
        try {

            $attributes = $request->validate([
                'user_id' => 'required',
                'nome' => 'nome'
            ]);

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

            $evento = $evento->load('administradores');

            return MelResponse::success("Evento cadastrado com sucesso!", $evento);

        } catch (ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            return MelResponse::error($e->getMessage());
        }
    }

    public function editarEvento(Request $request)
    {
        try {

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

            return MelResponse::success('Evento alterado com sucesso.', $evento);
        } catch (ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

    public function retornarAdministradores($evento_id)
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

    public function cadastrarEdicaoEvento(Request $request)
    {
        try {

            $attributes = $request->vaidate([
                'evento_id' => 'required',
                'nome' => 'required'
            ]);

            $eventoExistente = Evento::find($attributes['evento_id']);

            if (!$eventoExistente) {
                throw new \Exception("Não existe evento cadastrado com o ID " . $attributes['evento_id'] . "!");
            }

            DB::beginTransaction();

            $eventoEdicao = new EventoEdicao();
            $eventoEdicao->nome = $attributes['nome'];
            $eventoExistente->edicoes()->save($eventoEdicao);

            DB::commit();

            return MelResponse::success("Edição de evento cadastrada com sucesso!", $eventoEdicao);
        } catch (ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            return MelResponse::error($e->getMessage());
        }
    }

    public function editarEdicaoEvento(Request $request)
    {
        try {

            $attributes = $request->validate([
                'edicao_id' => 'required',
                'nome' => 'required',
            ]);

            $edicao = EventoEdicao::find($attributes['edicao_id']);

            if (!$edicao) {
                throw new \Exception("Nenhuma edição de evento com o id " . $attributes['edicao_id'] . " encontrado.");
            }

            $edicao->nome = $attributes['nome'];
            $edicao->save();

            return MelResponse::success('Edição de evento alterado com sucesso.', $edicao);
        } catch (ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

    public function removerEdicaoEvento(Request $request)
    {
        try {

            $attributes = $request->validate([
                'edicao_id' => 'required'
            ]);


            $edicao = EventoEdicao::find($attributes['edicao_id']);

            if (!$edicao) {
                throw new \Exception("Nenhuma edição de evento com o id " . $attributes['edicao_id'] . " encontrado.");
            }

            $edicao->ativo = 0;
            $edicao->save();

            return MelResponse::success(null, $edicao);
        } catch (ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

    public function retornarEvento($evento_id)
    {
        try {

            $msg = "Informações do evento obtidas com sucesso.";

            /** @var Evento $evento */
            $evento = Evento::find($evento_id);

            if (!$evento) {
                $msg = "Nenhum evento com o id " . $evento_id . " encontrado.";
            }

            return MelResponse::success($msg, $evento);
        } catch (ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

    public function retornarEdicoesEvento($evento_id)
    {
        try {
            $edicoes = EventoEdicao::where('evento_id', $evento_id)
                ->where('ativo', 1)
                ->get();

            return MelResponse::success("", $edicoes);
        } catch (ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

    public function retornarEdicaoEvento($edicao_id)
    {
        try {
            $edicao = EventoEdicao::find($edicao_id);

            if ($edicao->ativo === 0) {
                throw new \Exception("Evento informado foi removido do sistema.");
            }

            return MelResponse::success(null, $edicao);
        } catch (ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

    public function retornarEdicoesEventoUsuario(Request $request)
    {
        try {

            $attributes = $request->required([
                'user_id' => 'required'
            ]);

            $evento = Evento::query()
                ->join('evento_administrador', 'evento_id', 'evento.id')
                ->where('evento_administrador.user_id', '=', $attributes['user_id'])
                ->select('evento.*')
                ->get();

            $evento->load([
                'edicoes' => function ($query) {
                    $query->where('ativo', 1);
                }
            ]);

            return MelResponse::success("", $evento);
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

    public function retornarEmpresaDeEvento(Request $request)
    {
        try {
            $attributes = $request->validate([
                'evento_id' => 'required'
            ]);

            $evento = Evento::find($attributes['evento_id']);
            $evento->load('empresas');
            $empresas = $evento->empresas;

            return MelResponse::success("", $empresas);
        } catch (ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

}
