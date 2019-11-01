<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventoEdicao extends Model
{
    protected $table = 'evento_edicao';

    public function evento()
    {
        return $this->belongsTo('App\Evento','evento_id');
    }
}
