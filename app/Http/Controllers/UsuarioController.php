<?php

namespace App\Http\Controllers;

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
            $user = User::where('email', $email)->get();
            if ($user) {
                throw new \Exception("Este e-mail já foi cadastrado em nosso sistema. [" . $email . "].");
            }
            $user = new User();
            $user->name = $username;
            $user->email = $email;
            $user->password = Hash::make($password);
            $user->save();
            return MelResponse::success("Usuário cadastrado com sucesso!", $user);
        } catch (Exception $e) {
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
            $user = User::find($user_id);
            if (!$user) {
                throw new \Exception("Usuário não encontrado em nosso sistema. [" . $user_id . "].");
            }
            $user->id = $user_id;
            $user->name = $username;
            $user->email = $email;
            $user->password = Hash::make($password);
            $user->save();
            return MelResponse::success("Usuário alterado com sucesso!", $user);
        } catch (Exception $e) {
            return MelResponse::error("Erro ao editar as informações do usuário.", $e->getMessage());
        }
    }

    public function retornarUsuarioPorNomeEmail()
    {
        try {
            $search = request('search');
            $user = DB::table('users')
                ->where('name', 'like', $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%')
                ->get();
            if ($user->isEmpty()) {
                return MelResponse::error("Nenhum registro encontrado para o valor informado.", $search);
            }
            return MelResponse::success(null, $user);
        } catch (Exception $e) {
            return MelResponse::error("Não foi possível retornar o usuário informado.", $e->getMessage());
        }
    }
}
