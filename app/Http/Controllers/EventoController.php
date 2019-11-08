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

            $attributes = $request->validate([
                'user_id' => 'required',
                'nome' => 'nome'
            ]);

            $evento = Evento::find($attributes['evento_id']);

            if (!$evento) {
                throw new \Exception("Nenhum evento com o id " . $attributes['evento_id'] . " encontrado.");
            }

            $evento->nome = $attributes['nome'];
            $evento->save();

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

            $msg = "Informações do evento obtidas com sucesso.";

            /** @var Evento $evento */
            $evento = Evento::find($evento_id);

            if (!$evento) {
                $msg = "Nenhum evento com o id " . $evento_id . " encontrado.";
            }

            return MelResponse::success($msg, $evento);
        } catch (ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

    public function retornarEdicoesEvento($evento_id)
    {
        try {
            $edicoes = EventoEdicao::where('evento_id', $evento_id)
                ->get();

            return MelResponse::success("", $edicoes);
        } catch (ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

    public function retornarEventosUsuario($user_id)
    {
        try {

            $evento = DB::table('evento')
                ->join('evento_administrador', 'evento_id', 'evento.id')
                ->where('evento_administrador.user_id', '=', $user_id)
                ->select('evento.*')
                ->get();

            return MelResponse::success("Eventos encontrados para o usuário.", $evento);
        } catch (ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

    public function retornarEmpresaDeEvento(Request $request)
    {
        try {
            $attributes = $request->validate([
                'evento_id' => 'required'
            ]);

            $evento = Evento::find($attributes['evento_id']);
            $evento->load('empresas');
            $empresas = $evento->empresas;

            return MelResponse::success("", $empresas);
        } catch (ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }
}
