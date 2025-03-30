<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnidadeEndereco extends Model
{
    protected $table = 'unidade_endereco';

    protected $fillable = ['unid_id', 'end_id'];
}
