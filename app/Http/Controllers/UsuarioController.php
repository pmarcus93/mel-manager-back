<?php

namespace App\Http\Controllers;

use App\User;
use App\Response\MelResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UsuarioController extends Controller
{
    public function validandoDados($request)
    {
        $validador = Validator::make($request, [
            "name" => "required|string|max:255",
            "email" => "required|string|email|max:255|unique:users",
            "password" => "required|string|min:8|confirmed",
        ]);
        return $validador;
    }

    public function cadastrarUsuario(){

        try{
            $username = request('name');
            $email = request('email');
            $password = request('password');

            $validarDados = $this->validandoDados([$username,$email,$password]);

            if ($validarDados->fails()){
                return MelResponse::error("Erro ao validar os dados informados!", "");
            }

            $usuario = User::find($email);

            if ($usuario) {
                throw new \Exception("Este e-mail já foi cadastrado em nosso sistema. [" . $email . "].");
            }

            $usuario = new User();
            $usuario->name = $username;
            $usuario->email = $email;
            $usuario->password = $password;

            DB::beginTransaction();
            User::create([
                 'name' => $usuario['name'],
                 'email' => $usuario['email'],
                 'password' => Hash::make($usuario['password']),
              ]);
            DB::commit();
            return MelResponse::success("Usuário cadastrado com sucesso!", $usuario);


        }catch (Exception $e){
            DB::rollBack();
            return MelResponse::error("Erro ao cadastrar usuário.", $e->getMessage());
        }
    }

}
