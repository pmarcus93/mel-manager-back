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
            $fluxoCaixa = $this->cadastrarFluxoCaixa($request);
            return MelResponse::success("Fluxo de caixa cadastrado com sucesso.", $fluxoCaixa);
        } catch (ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            return MelResponse::error($e->getMessage());
        }
    }
}
