<?php


namespace App\Business;


use App\Response\MelResponse;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioBusiness
{
    /**
     * Cadastra um usuário da tabela users.
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function cadastrarUsuario(Request $request) {

        $attributes = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $telefone = $request->get('telefone');
        /** @var User $user */
        $user = User::where('email', $attributes['email'])->first();

        if ($user) {
            throw new \Exception("E-mail já cadastrado.");
        }

        $user = new User();
        $user->name = $attributes['name'];
        $user->email = $attributes['email'];
        $user->password = Hash::make($attributes['password']);

        if ($telefone) {
            $user->telefone = $telefone;
        }

        $user->save();
        return MelResponse::success("Usuário cadastrado com sucesso!", $user);
    }


}
