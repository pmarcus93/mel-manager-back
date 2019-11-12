<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * @property array|Request|string nome
 * @method static find(array|Request|string $categoria_id)
 */
class Categoria extends Model
{
    protected $table = 'categoria';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','ativo',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at','updated_at',
    ];

    public function fluxosCaixa()
    {
        return $this->hasMany('App\FluxoCaixa','categoria_id');
    }
}
