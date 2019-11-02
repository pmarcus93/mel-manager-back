<?php


namespace App\Business;


use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioBusiness
{
    /**
     * Cadastra um usuário da tabela users.
     * @param Request $request
     * @return User
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
        return $user;
    }

    /**
     * Edita as informações de usuário na tabela users.
     * @param Request $request
     * @return User
     * @throws \Exception
     */
    public function editarUsuario(Request $request) {

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
        return $user;
    }


}
