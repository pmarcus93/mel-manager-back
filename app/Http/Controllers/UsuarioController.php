<?php

namespace App\Http\Controllers;

use App\Telefone;
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
            $telefonesAdd = [];

            if ($user) {
                throw new \Exception("E-mail já cadastrado.");
            }

            DB::beginTransaction();

            $user = new User();
            $user->name = $attributes['name'];
            $user->email = $attributes['email'];
            $user->password = Hash::make($attributes['password']);
            $user->save();

            $telefones = \request('telefones');

            if ($telefones) {
                foreach ($telefones as $telefone) {
                    $novoTelefone = Telefone();
                    $novoTelefone->numero = $telefone;
                    $novoTelefone->save();
                    $telefonesAdd[] = $novoTelefone->id;
                }
                $user->telefones()->sync($telefonesAdd);
            }

            DB::commit();

            $user = $user->load('telefones');
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

            $user->save();
            // Aqui vem o código de edição de telefones que foi removido no branch do hotfix.
            DB::commit();
            return MelResponse::success("Usuário alterado com sucesso!", $user);
        } catch (\Exception $e) {
            DB::rollBack();
            return MelResponse::error($e->getMessage());
        }
    }

    public function cadastrarTelefone()
    {
        try {
            $user_id = request('user_id');
            $telefone = request('telefone');

            $user = User::find($user_id);

            if (!$user) {
                throw new \Exception("Usuário não econtrado!");
            }

            if (!$telefone) {
                throw new \Exception("Você precisa informar o telefone!");
            }

            DB::beginTransaction();

            $telefoneAdd = new Telefone();
            $telefoneAdd->numero = $telefone;
            $telefoneAdd->save();
            $user->telefones()->attach($telefoneAdd->id);

            DB::commit();

            $user = $user->load('telefones');
            return MelResponse::success("Telefone cadastrado com sucesso!", $user);
        } catch (\Exception $e) {
            DB::rollBack();
            return MelResponse::error($e->getMessage());
        }
    }

    public function editarTelefone()
    {
        try {
            $user_id = request('user_id');
            $telefone_id = request('telefone_id');
            $telefone_numero = request('telefone_numero');

            $user = User::find($user_id);
            $telefone = Telefone::find($telefone_id);

            if (!$user) {
                throw new \Exception("Usuário não econtrado!");
            }

            if (!$telefone) {
                throw new \Exception("Telefone não encontrado!");
            }

            if (!$telefone_numero) {
                throw new \Exception("Você deve informar o número do telefone!");
            }

            $telefone->numero = $telefone_numero;
            $telefone->save();

            $user = $user->load('telefones');
            return MelResponse::success("Telefone alterado com sucesso!", $user);
        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

    public function removerTelefone()
    {
        try {
            $user_id = request('user_id');
            $telefone_id = request('telefone_id');

            $user = User::find($user_id);
            $telefone = Telefone::find($telefone_id);

            if (!$user) {
                throw new \Exception("Usuário não econtrado!");
            }

            if (!$telefone) {
                throw new \Exception("Telefone não encontrado!");
            }

            DB::beginTransaction();

            $user->telefones()->detach($telefone_id);
            $telefone->delete();

            DB::commit();

            $user = $user->load('telefones');
            return MelResponse::success("Telefone excluído com sucesso!", $user);
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

            $user = $user->load('telefones');
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

            $user = $user->load('telefones');
            return MelResponse::success(null, $user);

        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

}
