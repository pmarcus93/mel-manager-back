<?php

namespace App\Http\Controllers;

use App\Business\EventoEdicaoBusiness;
use App\Response\MelResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EventoEdicaoController extends Controller
{
    /** @var EventoEdicaoBusiness */
    private $eventoEdicaoBusiness;

    public function __construct()
    {
        $this->eventoEdicaoBusiness = new EventoEdicaoBusiness();
    }

    public function retornarEventoEdicao($eventoedicao_id)
    {
        try {
            $eventoEdicao = $this->eventoEdicaoBusiness->retornarEventoEdicao($eventoedicao_id);
            return MelResponse::success("Edição de evento encontrada.", $eventoEdicao);
        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

    public function cadastrarEventoEdicao(Request $request)
    {
        try {
            $eventoEdicao = $this->eventoEdicaoBusiness->cadastrarEventoEdicao($request);
            return MelResponse::success("Edição de evento cadastrada com sucesso.", $eventoEdicao);
        } catch (ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            return MelResponse::error($e->getMessage());
        }
    }

    public function editarEventoEdicao(Request $request)
    {
        try {
            $eventoEdicao = $this->eventoEdicaoBusiness->editarEventoEdicao($request);
            return MelResponse::success('Edição de evento atualizada.', $eventoEdicao);
        } catch (ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

    public function removerEventoEdicao(Request $request)
    {
        try {
            $edicaoRemovida = $this->eventoEdicaoBusiness->removerEventoEdicao($request);
            return MelResponse::success("Edição de evento removida com sucesso.", $edicaoRemovida);
        } catch (ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

}
