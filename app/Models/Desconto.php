<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Desconto extends Model
{
    protected $table = "desconto";
    protected $fillable = [
        'id_funcionario',
        'porcent_desconto',
        'valor_minimo',
    ];

    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class, 'id_funcionario');
    }
}
