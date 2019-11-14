<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static find(array|\Illuminate\Http\Request|string $empresa_id)
 */
class Empresa extends Model
{
    Use SoftDeletes;

    protected $table = 'empresa';

    protected $fillable = [
       'nome','telefone',
    ];

    protected $hidden = [
        'created_at','updated_at','deleted_at',
    ];

    public function eventos()
    {
        return $this->belongsToMany('App\Evento','evento_empresa','empresa_id','evento_id');
    }
}
