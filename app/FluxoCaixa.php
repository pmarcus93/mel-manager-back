<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static find($fluxoCaixa_id)
 */
class FluxoCaixa extends Model
{
    use SoftDeletes;

    protected $table = 'fluxo_caixa';

    public function eventoEdicao()
    {
        return $this->belongsTo('App\EventoEdicao','eventoedicao_id');
    }

    public function categoria()
{
    return $this->belongsTo('App\Categoria','categoria_id');
}
}
