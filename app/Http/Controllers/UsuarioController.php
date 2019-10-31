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
                    $novoTelefone = new Telefone();
                    $novoTelefone->numero = $telefone;
                    $novoTelefone->save();
                    $telefonesAdd[] = $novoTelefone->id;
                }
                $user->telefones()->sync($telefonesAdd);
            }

            DB::commit();

            $user = $user->load('telefones');
            return MelResponse::success("Usuário cadastrado com sucesso.", $user);
        } catch (ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

    public function editarUsuario(Request $request)
    {
        try {

            $attributes = $request->validate([
                'user_id' => 'required',
                'name' => 'required',
                'email' => 'required|email'
            ]);

            $password = request('password');

            /** @var User $user */
            $user = User::find($attributes['user_id']);

            if (!$user) {
                throw new \Exception("Usuário não econtrado para edição.");
            }

            DB::beginTransaction();

            $user->name = $attributes['name'];
            $user->email = $attributes['email'];

            if ($password) {
                $user->password = Hash::make($password);
            }

            $user->save();
            DB::commit();
            return MelResponse::success("Usuário alterado com sucesso.", $user);
        } catch (ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            return MelResponse::error($e->getMessage());
        }
    }

    public function cadastrarTelefone(Request $request)
    {
        try {

            $attributes = $request->validate([
                'user_id' => 'required',
                'telefones' => 'required'
            ]);

            $telefonesAdd = [];

            /** @var User $user */
            $user = User::find($attributes['user_id']);

            if (!$user) {
                throw new \Exception("Usuário não econtrado.");
            }

            DB::beginTransaction();

            foreach ($attributes['telefones'] as $telefone) {
                $novoTelefone = new Telefone();
                $novoTelefone->numero = $telefone;
                $novoTelefone->save();
                $telefonesAdd[] = $novoTelefone->id;
            }

            $user->telefones()->attach($telefonesAdd);

            DB::commit();

            $user = $user->load('telefones');
            return MelResponse::success("Telefone(s) cadastrado(s) com sucesso.", $user);
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
