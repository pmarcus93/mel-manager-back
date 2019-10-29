<?php

namespace App\Http\Controllers;

use App\Telefone;
use App\User;
use Exception;
use App\Response\MelResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function cadastrarUsuario()
    {
        try {
            $username = request('name');
            $email = request('email');
            $password = request('password');
            $telefones = request('telefones');

            $user = User::where('email', $email)->first();
            $telefonesAdd = [];

            if (!$username || !$email || !$password) {
                throw new Exception("É necessário informar nome, email e senha.");
            }

            if ($user) {
                throw new Exception("E-mail já cadastrado.");
            }

            DB::beginTransaction();

            $user = new User();
            $user->name = $username;
            $user->email = $email;
            $user->password = Hash::make($password);
            $user->save();

            if ($telefones) {
                foreach ($telefones as $telefone) {
                    $telefoneAdd = new Telefone();
                    $telefoneAdd->numero = $telefone;
                    $telefoneAdd->save();
                    $telefonesAdd[] = $telefoneAdd->id;
                }
                $user->telefones()->sync($telefonesAdd);
            }

            DB::commit();

            $user = $user->load('telefones');

            return MelResponse::success("Usuário cadastrado com sucesso!", $user);
        } catch (Exception $e) {
            DB::rollBack();
            return MelResponse::error($e->getMessage());
        }
    }

    public function editarUsuario()
    {
        try {
            $user_id = request('user_id');
            $name = request('name');
            $email = request('email');
            $password = request('password');

            $user = User::find($user_id);

            if (!$user) {
                throw new Exception("Usuário não econtrado para edição!");
            }

            if ($name) {
                $user->name = $name;
            }

            if ($email) {
                $user->email = $email;
            }

            if ($password) {
                $user->password = Hash::make($password);
            }

            $user->save();

            return MelResponse::success("Usuário alterado com sucesso!", $user);
        } catch (Exception $e) {
            return MelResponse::error("Erro ao editar as informações do usuário.", $e->getMessage());
        }
    }

    public function cadastrarTelefone()
    {
        try {
            $user_id = request('user_id');
            $telefones = request('telefones');

            $user = User::find($user_id);

            if (!$user) {
                throw new Exception("Usuário não econtrado!");
            }

            if (!$telefones) {
                throw new Exception("Você precisa informar o telefone!");
            }

            DB::beginTransaction();

            if ($telefones) {
                foreach ($telefones as $telefone) {
                    $telefoneAdd = new Telefone();
                    $telefoneAdd->numero = $telefone;
                    $telefoneAdd->save();
                    $telefonesAdd[] = $telefoneAdd->id;
                }
                $user->telefones()->sync($telefonesAdd);
            }

            DB::commit();

            $user = $user->load('telefones');

            return MelResponse::success("Telefone cadastrado com sucesso!", $user);
        } catch (Exception $e) {
            DB::rollBack();
            return MelResponse::error("Erro ao cadastrar telefone.", $e->getMessage());
        }
    }

    public function editarTelefone()
    {
        try {
            $user_id = request('user_id');
            $telefones = request('telefones');

            $user = User::find($user_id);

            if (!$user) {
                throw new Exception("Usuário não econtrado!");
            }

            if (!$telefones) {
                throw new Exception("Você precisa informar o telefone!");
            }

            if ($telefones) {
                foreach ($telefones as $telefone) {
                    $telefoneEdit = Telefone::find($telefone['id']);
                    if (!$telefoneEdit) {
                        continue;
                    }
                    $telefoneEdit->numero = $telefone['numero'];
                    $telefoneEdit->save();
                }
            }

            $user = $user->load('telefones');

            return MelResponse::success("Telefone alterado com sucesso!", $user);
        } catch (Exception $e) {
            return MelResponse::error("Erro ao alterar telefone.", $e->getMessage());
        }
    }

    public function removerTelefone()
    {
        try {
            $user_id = request('user_id');
            $telefones = request('telefones');

            $user = User::find($user_id);

            if (!$user) {
                throw new Exception("Usuário não econtrado!");
            }

            if (!$telefones) {
                throw new Exception("Você precisa informar o telefone!");
            }

            DB::beginTransaction();

            if ($telefones) {
                foreach ($telefones as $telefone) {
                    $telefoneDel = Telefone::find($telefone['id']);
                    if (!$telefoneDel) {
                        continue;
                    }
                    $telefoneDel->ativo = 0;
                    $telefoneDel->save();
                    $telefonesAdd[] = $telefoneDel->id;
                }
                $user->telefones()->detach($telefonesAdd);
            }

            DB::commit();

            $user = $user->load('telefones');

            return MelResponse::success("Telefone removido com sucesso!", $user);
        } catch (Exception $e) {
            DB::rollBack();
            return MelResponse::error("Erro ao remover telefone.", $e->getMessage());
        }
    }

    public function retornarUsuarioPorNomeEmail()
    {
        try {
            $search = request('search');
            $limiteRetorno = request('qtd');

            if ($limiteRetorno < 1) {
                $limiteRetorno = 1;
            }

            $user = User::where('name', 'like', $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%')
                ->paginate($limiteRetorno);

            if (!$user) {
                throw new Exception("Nenhum registro encontrado para o valor informado!");
            }

            $user = $user->load('telefones');

            return MelResponse::success(null, $user);

        } catch (Exception $e) {
            return MelResponse::error("Não foi possível retornar o usuário informado.", $e->getMessage());
        }
    }

    public function retornarUsuarioPorID()
    {
        try {
            $user_id = request('id');
            $user = User::find($user_id);

            if (!$user) {
                throw new Exception("Nenhum registro encontrado para o valor informado!");
            }

            $user = $user->load('telefones');

            return MelResponse::success(null, $user);

        } catch (Exception $e) {
            return MelResponse::error("Não foi possível retornar o usuário informado.", $e->getMessage());
        }
    }

}
