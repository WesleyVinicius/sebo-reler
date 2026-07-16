<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Autor extends Model
{
    protected $table = 'autor';
    protected  $fillable = [
        'nome',
    ];

    public function livros()
    {
        return $this->belongsToMany(Livro::class, 'livro_autor', 'id_autor', 'id_livro');
    }
}
