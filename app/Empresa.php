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
       'nome','telefone', 'evento_id'
    ];

    protected $hidden = [
        'created_at','updated_at','deleted_at',
    ];

}
