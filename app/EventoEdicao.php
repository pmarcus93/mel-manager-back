<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static where(string $string, $evento_id)
 */
/**
 * @method static find($eventoedicao_id)
 */
class EventoEdicao extends Model
{
    use SoftDeletes;

    protected $table = 'evento_edicao';

    public function evento()
    {
        return $this->belongsTo('App\Evento','evento_id');
    }

    public function fluxosCaixa()
    {
        return $this->hasMany('App\FluxoCaixa','eventoedicao_id');
    }
}
