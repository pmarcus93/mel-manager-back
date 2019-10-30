<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static join()
 */
class Evento extends Model
{
    protected $table = 'evento';

    public function empresas()
    {
        return $this->belongsToMany('App\Evento', 'evento_empresa', 'evento_id', 'empresa_id');
    }

    public function administrador()
    {
        return $this->belongsToMany('App\User', 'evento_administrador', 'evento_id', 'user_id');
    }

    public function edicoes()
    {
        return $this->hasMany('App\EventoEdicao', 'evento_id');
    }
}
