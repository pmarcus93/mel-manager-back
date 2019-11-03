<?php

namespace App\Http\Controllers;

use App\Business\UsuariosEventoBusiness;
use App\Evento;
use App\Response\MelResponse;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;

class UsuariosEventoController extends Controller
{
    /** @var UsuariosEventoBusiness */
    private $usuariosEventoBusiness;

    public function __construct()
    {
        $this->usuariosEventoBusiness = new UsuariosEventoBusiness();
    }

    public function retornarAdministradoresEvento($evento_id)
    {
        try {
            $administradores = $this->usuariosEventoBusiness->retornarAdministradoresEvento($evento_id);
            return MelResponse::success("Busca realizada com sucesso.", $administradores);
        } catch (ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

    public function vincularAdministradorEvento(Request $request)
    {
        try {
            $administradorVinculado = $this->usuariosEventoBusiness->vincularAdministradorEvento($request);
            return MelResponse::success("Administrador vinculado com sucesso.", $administradorVinculado);
        } catch (ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

    public function desvincularAdministradorEvento(Request $request)
    {
        try {
            $administradoDesvinculado = $this->desvincularAdministradorEvento($request);
            return MelResponse::success("Administrador desvinculado com sucesso.", $administradoDesvinculado);
        } catch (ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

}
