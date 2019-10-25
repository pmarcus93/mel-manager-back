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
}
