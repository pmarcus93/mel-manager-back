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

    /**
     * Retorna um usuário com base no user_id.
     * @param int $user_id
     * @return User
     * @throws \Exception
     */
    public function retornarUsuario(int $user_id) {
        /** @var User $user */
        $user = User::find($user_id);
        if (!$user) {
            throw new \Exception("Não há usuário cadastrado no sistema com esse id.");
        }
        return $user;
    }

    /**
     * Retorna um ou mais usuários com base em seu nome ou email.
     * TODO: Este método deverá retornar apenas 1 usuário com base apenas no seu email.
     * @param string $pesquisa
     * @param int $quantidade
     * @return User
     * @throws \Exception
     */
    public function retornarUsuarioPesquisa($pesquisa, $quantidade) {

        /** @var User $users */
        $users = User::where('name', 'like', $pesquisa . '%')
            ->orWhere('email', 'like', '%' . $pesquisa . '%')
            ->paginate($quantidade);

        if (!$users) {
            throw new \Exception("Nenhum registro encontrado para o valor informado!");
        }
        return $users;
    }

}
