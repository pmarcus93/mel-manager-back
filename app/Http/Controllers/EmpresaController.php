<?php

namespace App\Http\Controllers;

use App\Evento;
use App\Response\MelResponse;
use App\Empresa;
use App\Telefone;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

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

            $empresa = $empresa->load('telefones');
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
            $empresa_id = request('empresa_id');
            $empresa_nome = request('empresa_nome');
            $empresa = Empresa::find($empresa_id);
            $telefones = request('telefones');

            if (!$empresa) {
                throw new \Exception("Empresa não econtrada para edição!");
            }

            $empresa->nome = $empresa_nome;
            $empresa->save();

            $telefonesNew = array();
            if ($telefones) {
                $telefonesNew = array_column($telefones, 'id');
            }

            $telefonesOld = $empresa->telefones->pluck('id')->toArray();

            if ($telefones) {
                foreach ($telefones as $telefone) {
                    $telefoneEdit = Telefone::find($telefone['id']);
                    if (!$telefoneEdit) {
                        $telefoneEdit = new Telefone();
                    }
                    $telefoneEdit->numero = $telefone['numero'];
                    $telefoneEdit->save();
                    $telefonesEdit[] = $telefoneEdit->id;
                }
                $empresa->telefones()->sync($telefonesEdit);
            }

            if (!$telefones) {
                $empresa->telefones()->sync([]);
            }

            if ($deletados = array_diff_key($telefonesOld, $telefonesNew)) {
                Telefone::wherein('id', $deletados)->delete();
            }

            DB::commit();

            $empresa = $empresa->load('telefones');
            $empresa = $empresa->load('eventos');
            return MelResponse::success("Empresa alterada com sucesso!", $empresa);

        } catch (\Exception $e) {
            DB::rollBack();
            return MelResponse::error("Erro ao alter empresa.", $e->getMessage());
        }
    }

    public function retornarEmpresa()
    {
        try {
            $empresa_id = request('id');
            $empresa = Empresa::find($empresa_id);

            if (!$empresa) {
                throw new \Exception("ID informado não econtrado!");
            }

            $empresa = $empresa->load('telefones');
            $empresa = $empresa->load('eventos');
            return MelResponse::success(null, $empresa);

        } catch (\Exception $e) {
            return MelResponse::error("Não foi possível retornar os dados da empresa.", $e->getMessage());
        }
    }

    public function vincularEventoEmpresa()
    {
        try {
            $empresa_id = request('empresa_id');
            $evento_id = request('evento_id');

            $empresa = Empresa::find($empresa_id);
            $evento = Evento::find($evento_id);

            if (!$empresa) {
                throw new \Exception("ID da empresa informado não econtrado!");
            }

            if (!$evento) {
                throw new \Exception("ID do Evento informado não econtrado!");
            }

            $empresa->eventos()->sync($evento_id);
            $empresa = $empresa->load('eventos');

            return MelResponse::success("Evento vinculado a empresa com sucesso!", $empresa);

        } catch (\Exception $e) {
            return MelResponse::error("Não foi possível vincular evento à empresa.", $e->getMessage());
        }
    }

}
