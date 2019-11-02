<?php

namespace App\Http\Controllers;

use Exception;
use App\Evento;
use App\Response\MelResponse;
use App\Empresa;
use Illuminate\Support\Facades\DB;

class EmpresaController extends Controller
{
    public function cadastrarEmpresa()
    {
        try {
            $empresa_nome = request('nome');
            $empresa = new Empresa();
            $empresa->nome = $empresa_nome;
            $empresa->save();
            return MelResponse::success("Empresa cadastrada com sucesso!", $empresa);
        } catch (Exception $e) {
            DB::rollBack();
            return MelResponse::error($e->getMessage());
        }
    }

    public function editarEmpresa()
    {
        try {
            $empresa_id = request('empresa_id');
            $empresa_nome = request('empresa_nome');

            $empresa = Empresa::find($empresa_id);

            if (!$empresa) {
                throw new Exception("Empresa não encontrada para edição!");
            }

            $empresa->nome = $empresa_nome;
            $empresa->save();

            return MelResponse::success("Empresa alterada com sucesso!", $empresa);
        } catch (Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

    public function retornarEmpresa()
    {
        try {
            $empresa_id = request('id');
            $empresa = Empresa::find($empresa_id);

            if (!$empresa) {
                throw new Exception("ID informado não econtrado!");
            }

            $empresa = $empresa->load('eventos');
            return MelResponse::success(null, $empresa);
        } catch (Exception $e) {
            return MelResponse::error($e->getMessage());
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
                throw new Exception("ID da empresa informado não econtrado!");
            }

            if (!$evento) {
                throw new Exception("ID do Evento informado não econtrado!");
            }

            $empresa->eventos()->attach($evento_id);
            $empresa = $empresa->load('eventos');

            return MelResponse::success("Evento vinculado a empresa com sucesso!", $empresa);

        } catch (Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

    public function desvincularEventoEmpresa()
    {
        try {
            $empresa_id = request('empresa_id');
            $evento_id = request('evento_id');

            $empresa = Empresa::find($empresa_id);
            $evento = Evento::find($evento_id);

            if (!$empresa) {
                throw new Exception("ID da empresa informado não econtrado!");
            }

            if (!$evento) {
                throw new Exception("ID do Evento informado não econtrado!");
            }

            $empresa->eventos()->detach($evento_id);
            $empresa = $empresa->load('eventos');

            return MelResponse::success("Evento desvinculado a empresa com sucesso!", $empresa);

        } catch (Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

    public function retornarEventosDeEmpresa()
    {
        try {
            $empresa_id = request('empresa_id');
            $empresa = Empresa::find($empresa_id);

            if (!$empresa) {
                throw new Exception("ID da empresa informado não econtrado!");
            }

            $empresa = $empresa->load('eventos');

            if ($empresa->eventos->isEmpty()) {
                throw new Exception("Sem eventos vinculados a esta empresa!");
            }

            return MelResponse::success(null, $empresa);

        } catch (Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }
}
