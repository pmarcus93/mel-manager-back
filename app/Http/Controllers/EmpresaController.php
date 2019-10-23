<?php

namespace App\Http\Controllers;

use App\Response\MelResponse;
use Illuminate\Http\Request;
use App\Empresa;
use Illuminate\Support\Facades\DB;

class EmpresaController extends Controller
{

    public function cadastrarEmpresa(Request $request)
    {
        try {
            DB::beginTransaction();
            $dados = $request->all();
            $empresa = Empresa::create($dados);
            $empresa = Empresa::find($empresa->id);
            $empresa->telefones()->sync($empresa['telefone_id']);
            DB::commit();
            return MelResponse::success("Empresa cadastrada com sucesso!", $empresa);
        } catch (\Exception $e) {
            DB::rollBack();
            return MelResponse::error("Erro ao cadastrar empresa.", $e->getMessage());
        }


    }


}
