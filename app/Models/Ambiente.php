<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ambiente extends Model
{
    protected $fillable = [
        'nome',
        'descricao',
        'status',
        'tipo'
    ];

    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }
}
