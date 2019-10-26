<?php

namespace App\Http\Controllers;

use App\Telefone;
use App\User;
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

            if (!$username || !$email || $password) {
                throw new \Exception("É necessário informar nome, email e senha.");
            }

            if ($user) {
                throw new \Exception("E-mail já cadastrado em nosso sistema!");
            }

            DB::beginTransaction();

            $user = new User();
            $user->name = $username;
            $user->email = $email;
            $user->password = Hash::make($password);
            $user->save();

            if ($telefones) {
                foreach ($telefones as $telefone) {
                    $telefone = new Telefone();
                    $telefone->numero = $telefone;
                    $telefone->save();
                    $telefonesAdd[] = $telefone->id;
                }
                $user->telefones()->sync($telefonesAdd);
            }

            DB::commit();

            $user = $user->load('telefones');
            return MelResponse::success("Usuário cadastrado com sucesso!", $user);
        } catch (\Exception $e) {
            return MelResponse::error("Erro ao cadastrar usuário.", $e->getMessage());
        }
    }

    public function editarUsuario()
    {
        try {
            $user_id = request('user_id');
            $username = request('name');
            $email = request('email');
            $password = request('password');
            $telefones = request('telefones');

            $user = User::find($user_id);

            if (!$user) {
                throw new \Exception("Usuário não econtrado para edição!");
            }

            DB::beginTransaction();

            $user->name = $username;
            $user->email = $email;
            $user->password = Hash::make($password);
            $user->save();

            $telefonesNew = array();
            if ($telefones) {
                $telefonesNew = array_column($telefones, 'id');
            }

            $telefonesOld = $user->telefones->pluck('id')->toArray();

            if ($telefones) {
                foreach ($telefones as $telefone) {
                    $telefoneEdit = Telefone::find($telefone['id']);
                    if (!$telefoneEdit) {
                        $telefoneEdit = new Telefone();
                    }
                    $telefoneEdit->numero = $telefone['numero'];
                    $telefoneEdit->save();
                    $telefonesEdit[] = $telefoneEdit->id;
                }
                $user->telefones()->sync($telefonesEdit);
            }

            if (!$telefones) {
                $user->telefones()->sync([]);
            }

            if ($deletados = array_diff_key($telefonesOld, $telefonesNew)) {
                Telefone::wherein('id', $deletados)->delete();
            }
            DB::commit();

            $user = $user->load('telefones');
            return MelResponse::success("Usuário alterado com sucesso!", $user);

        } catch (\Exception $e) {
            DB::rollBack();
            return MelResponse::error("Erro ao editar as informações do usuário.", $e->getMessage());
        }
    }

    public function retornarUsuarioPorNomeEmail()
    {
        try {
            $search = request('search');
            $user = User::where('name', 'like', $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%')
                ->get();

            if (!$user) {
                throw new \Exception("Nenhum registro encontrado para o valor informado!");
            }

            $user = $user->load('telefones');
            return MelResponse::success(null, $user);

        } catch (\Exception $e) {
            return MelResponse::error("Não foi possível retornar o usuário informado.", $e->getMessage());
        }
    }

    public function retornarUsuarioPorID()
    {
        try {
            $user_id = request('id');
            $user = User::find($user_id);

            if (!$user) {
                throw new \Exception("Nenhum registro encontrado para o valor informado!");
            }

            $user = $user->load('telefones');
            return MelResponse::success(null, $user);

        } catch (\Exception $e) {
            return MelResponse::error("Não foi possível retornar o usuário informado.", $e->getMessage());
        }
    }

}
