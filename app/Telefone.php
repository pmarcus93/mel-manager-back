<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Telefone extends Model
{
    protected $table = 'telefone';

    public function empresas()
    {
        return $this->belongsToMany('App\Empresa','telefone_empresa','telefone_id','empresa_id');
    }
}
