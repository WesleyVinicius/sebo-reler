<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venda extends Model
{
    protected $table = 'venda';
    protected $fillable = [
        'id_funcionario',
        'id_cliente',
        'data',
        'valor_total',
        'valor_desconto',
    ];

    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class, 'id_funcionario');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    public function exemplares()
    {
        return $this->hasMany(Exemplar::class, 'id_venda');
    }
}
