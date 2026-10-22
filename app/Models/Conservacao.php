<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conservacao extends Model
{
    use HasFactory;

    protected $table = 'conservacao';
    protected $fillable = [
        'estado',
    ];

    public function exemplares()
    {
        return $this->hasMany(Exemplar::class, 'id_conservacao');
    }
}
