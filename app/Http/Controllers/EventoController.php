<?php

namespace App\Http\Controllers;

use App\Business\EventoBusiness;
use App\Evento;
use App\EventoEdicao;
use App\Response\MelResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EventoController extends Controller
{
    /** @var EventoBusiness */
    private $eventoBusiness;

    public function __construct()
    {
        $this->eventoBusiness = new EventoBusiness();
    }

    public function cadastrarEvento(Request $request)
    {
        try {
            $evento = $this->eventoBusiness->cadastrarEvento($request);
            return MelResponse::success("Evento cadastrado com sucesso!", $evento);
        } catch (ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            return MelResponse::error($e->getMessage());
        }
    }

    public function editarEvento(Request $request)
    {
        try {
            $evento = $this->eventoBusiness->editarEvento($request);
            return MelResponse::success('Evento alterado com sucesso.', $evento);
        } catch (ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

    public function retornarEvento($evento_id)
    {
        try {
            $evento = $this->eventoBusiness->retornarEvento($evento_id);
            return MelResponse::success("Evento encontrado.", $evento);
        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

    public function retornarEdicoesEvento($evento_id)
    {
        try {
            $edicoes = $this->eventoBusiness->retornarEdicoesEvento($evento_id);
            return MelResponse::success("Edições de evento encontradas.", $edicoes);
        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

    public function retornarEventosUsuario($user_id)
    {
        try {
            $evento = $this->eventoBusiness->retornarEventosUsuario($user_id);
            return MelResponse::success("Eventos encontrados para o usuário.", $evento);
        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

    public function retornarEmpresaDeEvento(Request $request)
    {
        try {
            $empresas = $this->eventoBusiness->retornarEmpresaDeEvento($request);
            return MelResponse::success("Empresas encontradas.", $empresas);
        } catch (ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }
}
