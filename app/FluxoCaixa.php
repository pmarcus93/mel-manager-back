<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FluxoCaixa extends Model
{
    use SoftDeletes;

    protected $table = 'fluxo_caixa';

    public function eventoEdicao()
    {
        return $this->belongsTo('App\EventoEdicao','eventoedicao_id');
    }
}
