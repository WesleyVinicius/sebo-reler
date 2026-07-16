<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Funcionario extends Model implements AuthenticatableContract
{
    use Authenticatable;

    protected $table = "funcionario";
    protected $fillable = [
        'usuario',
        'senha',
        'cpf',
        'rg',
        'data_nascimento',
        'sexo',
        'salario',
        'perfil',
    ];

    public function getAuthPassword()
    {
        return $this->senha;
    }

    public function descontos()
    {
        return $this->hasMany(Desconto::class, 'id_funcionario');
    }

    public function vendas()
    {
        return $this->hasMany(Venda::class, 'id_funcionario');
    }

    public function exemplares_venda()
    {
        return $this->hasMany(Exemplar::class, 'id_funcionario');
    }
}
