<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    protected $table = 'evento';

    public function empresas()
    {
        return $this->belongsToMany('App\Evento','evento_empresa','evento_id','empresa_id');
    }
}
