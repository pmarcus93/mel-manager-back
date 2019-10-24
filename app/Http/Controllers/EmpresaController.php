<?php

namespace App\Http\Controllers;

use App\Response\MelResponse;
use App\Empresa;
use App\Telefone;
use Illuminate\Support\Facades\DB;

class EmpresaController extends Controller
{
    public function cadastrarEmpresa()
    {
        try {
            DB::beginTransaction();
            $empresa_nome = request('nome');
            $empresa = new Empresa();
            $empresa->nome = $empresa_nome;
            $empresa->save();
            $telefones_numeros = request('telefones');
            foreach ($telefones_numeros as $telefone_numero) {
                $telefone = new Telefone();
                $telefone->numero = $telefone_numero;
                $telefone->save();
                $telefonesAdd[] = $telefone->id;
            }
            $empresa->telefones()->sync($telefonesAdd);
            DB::commit();
            $telefones[] = $empresa->telefones;
            $empresaDados[] = $empresa;
            array_merge($empresaDados, $telefones);
            return MelResponse::success("Empresa cadastrada com sucesso!", $empresaDados);
        } catch (\Exception $e) {
            DB::rollBack();
            return MelResponse::error("Erro ao cadastrar empresa.", $e->getMessage());
        }
    }

    public function editarEmpresa()
    {
        try {
            DB::beginTransaction();
            $empresa_id = request('empresa_id');
            $empresa_nome = request('empresa_nome');
            $empresa = Empresa::find($empresa_id);
            $telefones = request('telefones');

            if (!$empresa) {
                $data['id'] = $empresa_id;
                $data['nome'] = $empresa_nome;
                $dados = array_merge($data, $telefones);
                return MelResponse::warning("Empresa não encontrada para edição!", $dados);
            }

            $empresa->nome = $empresa_nome;
            $empresa->save();

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
            $telefones[] = $empresa->telefones;
            $empresaDados[] = $empresa;
            array_merge($empresaDados, $telefones);
            return MelResponse::success("Empresa alterada com sucesso!", $empresaDados);
        } catch (\Exception $e) {
            DB::rollBack();
            return MelResponse::error("Erro ao alter empresa.", $e->getMessage());
        }
    }

    public function retornarEmpresa()
    {
        try {
            $empresa_id = request('id');
            $empresa[] = Empresa::find($empresa_id);
            if (!$empresa) {
                return MelResponse::warning("O ID informado não foi encontrado!", $empresa_id);
            }
            $telefones[] = $empresa[0]->telefones;
            array_merge($empresa, $telefones);
            return MelResponse::success(null, $empresa);
        } catch (\Exception $e) {
            return MelResponse::error("Não foi possível retornar os dados da empresa.", $e->getMessage());
        }
    }

}
