<?php

namespace App\Http\Controllers;

use App\Business\UsuarioBusiness;
use App\User;
use App\Response\MelResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UsuarioController extends Controller
{

    private $usuarioBusiness;

    public function __construct()
    {
        $this->usuarioBusiness = new UsuarioBusiness();
    }

    public function cadastrarUsuario(Request $request)
    {
        try {
            return $this->usuarioBusiness->cadastrarUsuario($request);
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

            $telefone = request('telefone');

            /** @var User $user */
            $user = User::find($attributes['user_id']);

            if (!$user) {
                throw new \Exception("Usuário não econtrado para edição.");
            }

            $user->name = $attributes['name'];
            $user->email = $attributes['email'];

            if ($telefone) {
                $user->telefone = $telefone;
            }

            $user->save();
            return MelResponse::success("Usuário alterado com sucesso.", $user);
        } catch (ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            return MelResponse::error($e->getMessage());
        }
    }

    public function retornarUsuarioPesquisa($pesquisa, $quantidade)
    {
        try {

            $user = User::where('name', 'like', $pesquisa . '%')
                ->orWhere('email', 'like', '%' . $pesquisa . '%')
                ->paginate($quantidade);

            if (!$user) {
                throw new \Exception("Nenhum registro encontrado para o valor informado!");
            }

            return MelResponse::success(null, $user);
        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

    public function retornarUsuario($user_id)
    {
        try {
            $user = User::find($user_id);

            if (!$user) {
                throw new \Exception("Não há usuário cadastrado no sistema com esse id.");
            }

            return MelResponse::success(null, $user);
        } catch (ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

}
