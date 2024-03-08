<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ponto extends Model
{
    protected $table = 'ponto';
    protected $fillable = [
        'utilizador_id',
        'data',
        'entrada_manha',
        'saida_manha',
        'entrada_tarde',
        'saida_tarde',
        'colab_obs',
        'status',
        'tipo',
        'tipo_ponto_id',
    ];

    public function utilizador(){
        return $this->belongsTo('App\Utilizador', 'utilizador_id');
    }

    public function tipo_ponto(){
        return $this->belongsTo('App\TipoPonto', 'tipo_ponto_id');
    }
}
