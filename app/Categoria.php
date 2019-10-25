<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property array|Request|string nome
 */
class Categoria extends Model
{
    protected $table = 'categoria';
}
