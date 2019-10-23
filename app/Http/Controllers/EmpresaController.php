<?php

namespace App\Http\Controllers;

use App\Response\MelResponse;
use App\Empresa;
use App\Telefone;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class EmpresaController extends Controller
{

    public function cadastrarEmpresa()
    {
        try {
            DB::beginTransaction();
            $empresa_nome = Request::input('nome');
            $empresa = new Empresa();
            $empresa->nome = $empresa_nome;
            $empresa->save();
            $empresa = Empresa::find($empresa->id);
            $telefones_numeros = Request::input('telefones');
            foreach ($telefones_numeros as $telefone_numero) {
                $telefone = new Telefone();
                $telefone->numero = $telefone_numero;
                $telefone->save();
                $telefoneAdd[] = $telefone->id;
            }
            $empresa->telefones()->sync($telefoneAdd);
            DB::commit();
            return MelResponse::success("Empresa cadastrada com sucesso!", $empresa);
        } catch (\Exception $e) {
            DB::rollBack();
            return MelResponse::error("Erro ao cadastrar empresa.", $e->getMessage());
        }
    }

}
