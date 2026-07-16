<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exemplar extends Model
{
    protected $table = "exemplar";

    protected $fillable = [
        'id_livro',
        'id_conservacao',
        'id_venda',
        'id_funcionario',
        'preco_compra',
        'preco_venda',
        'status',
    ];

    public function livro()
    {
        return $this->belongsTo(Livro::class, 'id_livro');
    }

    public function conservacao()
    {
        return $this->belongsTo(Conservacao::class, 'id_conservacao');
    }

    public function venda()
    {
        return $this->belongsTo(Venda::class, 'id_venda');
    }

    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class, 'id_funcionario');
    }
}
