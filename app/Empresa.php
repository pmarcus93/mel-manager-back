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

    public function eventos()
    {
        return $this->belongsToMany('App\Evento','evento_empresa','empresa_id','evento_id');
    }
}
