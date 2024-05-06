<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ausencia extends Model
{
    protected $table = 'ausencia';
    protected $fillable = [
        'utilizador_id',
        'tipo_ausencia_id',
        'obs_colab',
        'obs_coord',
        'data',
        'hora_inicio',
        'hora_fim',
        'status',
        '_ativo',
        'horas_ausencia',
        'anexo',
    ];

    public function utilizador(){
        return $this->belongsTo('App\Utilizador', 'utilizador_id');
    }

    public function tipo_ausencia(){
        return $this->belongsTo('App\TiposAusencia', 'tipo_ausencia_id');
    }
}
