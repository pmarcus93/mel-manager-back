<?php

namespace App\Http\Controllers;

use App\Response\MelResponse;
use App\Business\EmpresaBusiness;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    /**@var EmpresaBusiness */
    private $empresaBusiness;

    public function __construct()
    {
        $this->empresaBusiness = new EmpresaBusiness();
    }

    public function cadastrarEmpresa(Request $request)
    {
        try {
            $empresa = $this->empresaBusiness->cadastrarEmpresa($request);
            return MelResponse::success("Empresa cadastrada com sucesso.", $empresa);
        } catch (\ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

    public function editarEmpresa(Request $request)
    {
        try {
            $empresa = $this->empresaBusiness->editarEmpresa($request);
            return MelResponse::success("Empresa atualizada.", $empresa);
        } catch (\ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

    public function removerEmpresa(Request $request)
    {
        try {
            $empresa = $this->empresaBusiness->removerEmpresa($request);
            return MelResponse::success("Empresa removida com sucesso.", $empresa);
        } catch (\ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

    public function retornarEmpresa($empresa_id)
    {
        try {
            $empresa = $this->empresaBusiness->retornarEmpresa($empresa_id);
            return MelResponse::success("Empresa encontrada.", $empresa);
        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

    public function retornarEmpresasEvento($evento_id)
    {
        try {
            $empresas = $this->empresaBusiness->retornarEmpresasEvento($evento_id);
            return MelResponse::success("Empresas encontradas.", $empresas);
        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

}
