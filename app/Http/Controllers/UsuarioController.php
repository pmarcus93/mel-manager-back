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
            DB::beginTransaction();
            $username = request('name');
            $email = request('email');
            $password = request('password');
            $user = User::where('email', $email)->first();
            $telefones_numeros = request('telefones');

            if ($user) {
                $data['usuario']['username'] = $username;
                $data['usuario']['email'] = $email;
                $data['usuario']['password'] = $password;
                $data['usuario']['telefones'] = $telefones_numeros;
                return MelResponse::warning("Este e-mail já foi cadastrado em nosso sistema.", $data);
            }

            $user = new User();
            $user->name = $username;
            $user->email = $email;
            $user->password = Hash::make($password);
            $user->save();

            foreach ($telefones_numeros as $telefone_numero) {
                $telefone = new Telefone();
                $telefone->numero = $telefone_numero;
                $telefone->save();
                $telefonesAdd[] = $telefone->id;
            }
            $user->telefones()->sync($telefonesAdd);
            DB::commit();

            $telefones[] = $user->telefones;
            $userDados[] = $user;
            array_merge($userDados, $telefones);
            return MelResponse::success("Usuário cadastrado com sucesso!", $userDados);

        } catch (\Exception $e) {
            return MelResponse::error("Erro ao cadastrar usuário.", $e->getMessage());
        }
    }

    public function editarUsuario()
    {
        try {
            DB::beginTransaction();
            $user_id = request('user_id');
            $username = request('name');
            $email = request('email');
            $password = request('password');
            $user = User::find($user_id);
            $telefones = request('telefones');

            if (!$user) {
                $data['usuario']['user_id'] = $user_id;
                $data['usuario']['name'] = $username;
                $data['usuario']['email'] = $email;
                $data['usuario']['telefones'] = $telefones;
                return MelResponse::warning("Usuário não encontrada para edição!", $data);
            }

            $user->id = $user_id;
            $user->name = $username;
            $user->email = $email;
            $user->password = Hash::make($password);
            $user->save();

            $telefonesNew = array_column($telefones, 'id');
            $telefonesOld = $user->telefones->pluck('id')->toArray();

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

            if ($deletados = array_diff_key($telefonesOld, $telefonesNew)) {
                Telefone::wherein('id', $deletados)->delete();
            }
            DB::commit();

            $user = User::find($user_id);
            $telefones[] = $user->telefones;
            $userDados[] = $user;
            array_merge($userDados, $telefones);
            return MelResponse::success("Usuário alterado com sucesso!", $userDados);

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
                ->first();

            if (!$user) {
                return MelResponse::error("Nenhum registro encontrado para o valor informado.", $search);
            }

            $telefones[] = $user->telefones;
            $userDados[] = $user;
            array_merge($userDados, $telefones);
            return MelResponse::success(null, $userDados);

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
                return MelResponse::error("Nenhum registro encontrado para o valor informado.", $user_id);
            }

            $telefones[] = $user->telefones;
            $userDados[] = $user;
            array_merge($userDados, $telefones);
            return MelResponse::success(null, $userDados);

        } catch (\Exception $e) {
            return MelResponse::error("Não foi possível retornar o usuário informado.", $e->getMessage());
        }
    }


}
