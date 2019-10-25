<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * @property array|Request|string nome
 */
class Categoria extends Model
{
    protected $table = 'categoria';
}
