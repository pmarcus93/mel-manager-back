<?php

namespace App\Http\Controllers;

use App\Categoria;
use App\Response\MelResponse;
use Illuminate\Http\Request;
use App\Business\CategoriaBusiness;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CategoriaController extends Controller
{

    /**var CategoriaBusiness*/
    private $categoriaBusiness;

    public function __construct()
    {
        $this->categoriaBusiness = new CategoriaBusiness();
    }

    public function cadastrarCategoria(Request $request)
    {
        try {
            $categoria = $this->categoriaBusiness->cadastrarCategoria($request);
            return MelResponse::success("Categoria cadastrada com sucesso.", $categoria);
        } catch (ValidationException $e) {
            return MelResponse::validationError($e->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            return MelResponse::error($e->getMessage());
        }
    }
}
