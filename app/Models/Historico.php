<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Historico extends Model
{
    use HasFactory;

    protected $table = 'historicos';

    protected $fillable = [
        'reserva_id',
        'alteracoes',
        'modificado_em',
        'usuario_id'
    ];

    /**
     * Relacionamento com a tabela de Reservas
     */
    public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'reserva_id');
    } //
}
