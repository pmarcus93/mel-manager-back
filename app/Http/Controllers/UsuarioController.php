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
            $novoUsuario = $this->usuarioBusiness->cadastrarUsuario($request);
            return MelResponse::success("Usuário cadastrado com sucesso.", $novoUsuario);
        } catch (ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

    public function editarUsuario(Request $request)
    {
        try {
            $usuarioEditado = $this->usuarioBusiness->editarUsuario($request);
            return MelResponse::success("Usuário editado com sucesso.", $usuarioEditado);
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
