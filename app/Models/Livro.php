<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Livro extends Model
{
    protected $table = "livro";

    protected $fillable = [
        'id_genero',
        'id_editora',
        'titulo',
    ];

    public function exemplares()
    {
        return $this->hasMany(Exemplar::class, 'id_livro');
    }

    public function genero()
    {
        return $this->belongsTo(Genero::class, 'id_genero');
    }

    public function editora()
    {
        return $this->belongsTo(Editora::class, 'id_editora');
    }

    public function autores()
    {
        return $this->belongsToMany(Autor::class, 'livro_autor', 'id_livro', 'id_autor');
    }
}
