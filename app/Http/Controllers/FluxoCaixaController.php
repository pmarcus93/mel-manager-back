<?php

namespace App\Http\Controllers;

use App\Business\FluxoCaixaBusiness;
use App\Response\MelResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FluxoCaixaController extends Controller
{
    /**@var FluxoCaixaBusiness */
    private $fluxoCaixaBusiness;

    public function __construct()
    {
        $this->fluxoCaixaBusiness = new FluxoCaixaBusiness();
    }

    public function cadastrarFluxoCaixa(Request $request)
    {
        try {
            $fluxoCaixa = $this->fluxoCaixaBusiness->cadastrarFluxoCaixa($request);
            return MelResponse::success("Fluxo de caixa cadastrado com sucesso.", $fluxoCaixa);
        } catch (ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            return MelResponse::error($e->getMessage());
        }
    }

    public function editarFluxoCaixa(Request $request)
    {
        try {
            $fluxoCaixa = $this->fluxoCaixaBusiness->editarFluxoCaixa($request);
            return MelResponse::success("Fluxo de caixa atualizado.", $fluxoCaixa);
        } catch (ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            return MelResponse::error($e->getMessage());
        }
    }

    public function removerFluxoCaixa(Request $request)
    {
        try {
            $fluxoCaixa = $this->fluxoCaixaBusiness->removerFluxoCaixa($request);
            return MelResponse::success("Fluxo de caixa removido com sucesso.", $fluxoCaixa);
        } catch (ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            return MelResponse::error($e->getMessage());
        }
    }

    public function retornarFluxoCaixa($fluxoCaixa_id)
    {
        try {
            $fluxoCaixa = $this->fluxoCaixaBusiness->retornarFluxoCaixa($fluxoCaixa_id);
            return MelResponse::success("Fluxo de caixa encontrado.", $fluxoCaixa);
        } catch (ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            return MelResponse::error($e->getMessage());
        }
    }

    public function retornarFluxosPorEdicaoEvento($edicaoEvento_id)
    {
        try {
            $fluxosCaixa = $this->fluxoCaixaBusiness->retornarFluxosPorEdicaoEvento($edicaoEvento_id);
            return MelResponse::success("Fluxos de caixa encontrados.", $fluxosCaixa);
        } catch (ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            return MelResponse::error($e->getMessage());
        }
    }
}
