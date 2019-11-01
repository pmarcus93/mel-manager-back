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
            DB::beginTransaction();
            $empresa_nome = request('nome');
            $empresa = new Empresa();
            $empresa->nome = $empresa_nome;
            $empresa->save();
            $telefones_numeros = request('telefones');

            foreach ($telefones_numeros as $telefone_numero) {
                $telefone = new Telefone();
                $telefone->numero = $telefone_numero;
                $telefone->save();
                $telefonesAdd[] = $telefone->id;
            }

            $empresa->telefones()->sync($telefonesAdd);
            DB::commit();

            $empresa = $empresa->load([
                'telefones' => function ($query) {
                    $query->where('ativo', 1);
                }
            ]);

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

            $empresa = $empresa->load([
                'telefones' => function ($query) {
                    $query->where('ativo', 1);
                }
            ]);

            $empresa = $empresa->load('eventos');
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

            $empresa = $empresa->load([
                'telefones' => function ($query) {
                    $query->where('ativo', 1);
                }
            ]);

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

    public function cadastrarTelefone()
    {
        try {
            $empresa_id = request('empresa_id');
            $telefones = request('telefones');

            $empresa = Empresa::find($empresa_id);

            if (!$empresa) {
                throw new Exception("Empresa não encontrada!");
            }

            if (!$telefones) {
                throw new Exception("Você precisa informar o telefone!");
            }

            DB::beginTransaction();

            if ($telefones) {
                foreach ($telefones as $telefone) {
                    $telefoneAdd = new Telefone();
                    $telefoneAdd->numero = $telefone;
                    $telefoneAdd->save();
                    $telefonesAdd[] = $telefoneAdd->id;
                }
                $empresa->telefones()->sync($telefonesAdd);
            }

            DB::commit();

            $empresa = $empresa->load([
                'telefones' => function ($query) {
                    $query->where('ativo', 1);
                }
            ]);

            return MelResponse::success(null, $empresa);
        } catch (Exception $e) {
            DB::rollBack();
            return MelResponse::error($e->getMessage());
        }
    }

    public function editarTelefone()
    {
        try {
            $empresa_id = request('empresa_id');
            $telefones = request('telefones');

            $empresa = Empresa::find($empresa_id);

            if (!$empresa) {
                throw new Exception("Empresa não encontrada!");
            }

            if (!$telefones) {
                throw new Exception("Você precisa informar o telefone!");
            }

            if ($telefones) {
                foreach ($telefones as $telefone) {
                    $telefoneEdit = Telefone::find($telefone['id']);
                    if (!$telefoneEdit) {
                        continue;
                    }
                    $telefoneEdit->numero = $telefone['numero'];
                    $telefoneEdit->save();
                }
            }

            $empresa = $empresa->load([
                'telefones' => function ($query) {
                    $query->where('ativo', 1);
                }
            ]);

            return MelResponse::success(null, $empresa);
        } catch (Exception $e) {
            return MelResponse::error($e->getMessage());
        }
    }

    public function removerTelefone()
    {
        try {
            $empresa_id = request('empresa_id');
            $telefones = request('telefones');

            $empresa = Empresa::find($empresa_id);

            if (!$empresa) {
                throw new Exception("Empresa não encontrada!");
            }

            if (!$telefones) {
                throw new Exception("Você precisa informar o telefone!");
            }

            DB::beginTransaction();

            if ($telefones) {
                foreach ($telefones as $telefone) {

                    if (!$empresa->telefones()->find($telefone['id'])) {
                        continue;
                    }
                    $empresa->telefones()->updateExistingPivot($telefone['id'], ['ativo' => 0]);
                }
            }

            DB::commit();

            $empresa = $empresa->load([
                'telefones' => function ($query) {
                    $query->where('ativo', 1);
                }
            ]);

            return MelResponse::success(null, $empresa);
        } catch (Exception $e) {
            DB::rollBack();
            return MelResponse::error($e->getMessage());
        }
    }

}
