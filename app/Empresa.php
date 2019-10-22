<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $table = 'empresa';

    public function telefones()
    {
        return $this->belongsToMany('App\Telefone','telefone_empresa','empresa_id','telefone_id');
    }
}
