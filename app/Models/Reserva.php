<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    protected $fillable = [
        'ambiente_id',
        'user_id',
        'data_reserva',
        'hora_inicio',
        'hora_fim',
        'status',
    ];

    public function ambiente()
    {
        return $this->belongsTo(Ambiente::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
