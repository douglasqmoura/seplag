<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServidorTemporario extends Model
{
    protected $table = 'servidor_temporario';
    protected $primaryKey = 'pes_id';
    public $incrementing = false;

    protected $fillable = ['pes_id', 'st_data_admissao', 'st_data_demissao'];

    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class, 'pes_id');
    }
}
