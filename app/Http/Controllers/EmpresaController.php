<?php

namespace App\Http\Controllers;

use Exception;
use App\Evento;
use App\Response\MelResponse;
use App\Empresa;
use App\Business\EmpresaBusiness;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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
        } catch (ValidationException $e) {
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
        } catch (ValidationException $e) {
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
        } catch (ValidationException $e) {
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

    public function vincularEventoEmpresa(Request $request)
    {
        try {
            $empresa = $this->empresaBusiness->vincularEventoEmpresa($request);
            return MelResponse::success("Evento vinculado a empresa.", $empresa);
        } catch (ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

    public function desvincularEventoEmpresa(Request $request)
    {
        try {
            $empresa = $this->empresaBusiness->desvincularEventoEmpresa($request);
            return MelResponse::success("Evento desvinculado da empresa.", $empresa);
        } catch (ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

}
