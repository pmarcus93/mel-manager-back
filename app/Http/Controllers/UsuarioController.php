<?php

namespace App\Http\Controllers;

use App\User;
use App\Response\MelResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UsuarioController extends Controller
{
    public function cadastrarUsuario(Request $request)
    {
        try {

            $attributes = $request->validate([
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required'
            ]);

            $user = User::where('email', $attributes['email'])->first();

            if ($user) {
                throw new \Exception("E-mail já cadastrado.");
            }

            $user = new User();
            $user->name = $attributes['name'];
            $user->email = $attributes['email'];
            $user->password = Hash::make($attributes['password']);

            if ($attributes['telefone']) {
                $user->telefone = $attributes['telefone'];
            }

            $user->save();

            return MelResponse::success("Usuário cadastrado com sucesso!", $user);
        } catch (ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
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
            $telefone = request('telefone');

            $user = User::find($user_id);

            if (!$user) {
                throw new \Exception("Usuário não econtrado para edição!");
            }

            DB::beginTransaction();

            if ($name) {
                $user->name = $name;
            }

            if ($email) {
                $user->email = $email;
            }

            if ($password) {
                $user->password = Hash::make($password);
            }

            if ($telefone) {
                $user->telefone = $telefone;
            }

            $user->save();
            DB::commit();
            return MelResponse::success("Usuário alterado com sucesso!", $user);
        } catch (\Exception $e) {
            DB::rollBack();
            return MelResponse::error($e->getMessage());
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
                throw new \Exception("Nenhum registro encontrado para o valor informado!");
            }

            return MelResponse::success(null, $user);

        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
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

            return MelResponse::success(null, $user);

        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

}
