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
            $telefones_numeros = Request::input('telefones');
            foreach ($telefones_numeros as $telefone_numero) {
                $telefone = new Telefone();
                $telefone->numero = $telefone_numero;
                $telefone->save();
                $telefonesAdd[] = $telefone->id;
            }
            $empresa->telefones()->sync($telefonesAdd);
            DB::commit();
            return MelResponse::success("Empresa cadastrada com sucesso!", $empresa);
        } catch (\Exception $e) {
            DB::rollBack();
            return MelResponse::error("Erro ao cadastrar empresa.", $e->getMessage());
        }
    }

    public function editarEmpresa()
    {
        try {
            DB::beginTransaction();
            $empresa_id = Request::input('empresa_id');
            $empresa_nome = Request::input('empresa_nome');
            $empresa = Empresa::find($empresa_id);
            $empresa->nome = $empresa_nome;
            $empresa->save();

            $telefones = Request::input('telefones');
            $telefonesNew = array_column($telefones, 'id');
            $telefonesOld = Telefone::find($empresa->id)->pluck('id')->toArray();

            foreach ($telefones as $telefone) {
                $telefoneEdit = Telefone::find($telefone['id']);
                $telefoneEdit->numero = $telefone['numero'];
                $telefoneEdit->save();
                $telefonesEdit[] = $telefoneEdit->id;
            }
            $empresa->telefones()->sync($telefonesEdit);

            if ($deletados = array_diff_key($telefonesOld, $telefonesNew)) {
                Telefone::wherein('id', $deletados)->delete();
            }

            DB::commit();
            return MelResponse::success("Empresa alterada com sucesso!", $empresa);
        } catch (\Exception $e) {
            DB::rollBack();
            return MelResponse::error("Erro ao alter empresa.", $e->getMessage());
        }
    }

}
