<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\RegisterController;
use App\Response\MelResponse;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use mysql_xdevapi\Exception;
use function Illuminate\Support\Facades\Auth;

class UsuarioController extends Controller
{

    public function cadastrarUsuario(){

        try{

            $username = \request('name');
            $email = \request('email');
            $password = \request('password');

            $usuario = User::find($email);

            if ($usuario) {
                throw new \Exception("Este e-mail já foi cadastrado em nosso sistema. [" . $email . "].");
            }



            $usuario = new User();
            $usuario->name = $username;
            $usuario->email = $email;
            $usuario->password = $password;

            if (Auth::validate($usuario)){
                DB::beginTransaction();
                 User::create([
                    'name' => $usuario['name'],
                    'email' => $usuario['email'],
                    'password' => Hash::make($usuario['password']),
                ]);
                DB::commit();
                return MelResponse::success("Usuário cadastrado com sucesso!", $usuario);
            }
            return MelResponse::error("Erro ao validar os dados informados!", $usuario);

        }catch (\Exception $e){
            DB::rollBack();
            return MelResponse::error("Erro ao cadastrar usuário.", $e->getMessage());
        };
    }

}
