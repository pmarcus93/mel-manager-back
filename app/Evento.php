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
        return $this->belongsToMany('App\Empresa','evento_empresa','evento_id','empresa_id');
    }

    public function administradores()
    {
        return $this->belongsToMany('App\User','evento_administrador','evento_id','user_id');
    }

    public function edicoes()
    {
        return $this->hasMany('App\EventoEdicao','evento_id');
    }
}
