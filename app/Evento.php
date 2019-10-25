<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static find(array|\Illuminate\Http\Request|string $evento_id)
 */
class Evento extends Model
{
    protected $table = 'evento';

    public function empresas()
    {
        return $this->belongsToMany('App\Evento','evento_empresa','evento_id','empresa_id');
    }
}
