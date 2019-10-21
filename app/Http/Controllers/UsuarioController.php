<?php

namespace App\Http\Controllers;

use App\User;
use App\Response\MelResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{

    public function cadastrarUsuario(){

        try{
            $username = request('name');
            $email = request('email');
            $password = request('password');

            $usuario = User::find($email);

            if ($usuario) {
                throw new \Exception("Este e-mail já foi cadastrado em nosso sistema. [" . $email . "].");
            }

            $usuario = new User();
            $usuario->name = $username;
            $usuario->email = $email;
            $usuario->password = Hash::make($password);

            DB::beginTransaction();
            $usuario->save();
            DB::commit();
            return MelResponse::success("Usuário cadastrado com sucesso!", $usuario);


        }catch (Exception $e){
            DB::rollBack();
            return MelResponse::error("Erro ao cadastrar usuário.", $e->getMessage());
        }
    }

}
