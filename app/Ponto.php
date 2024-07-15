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
        'entrada_noite',
        'saida_noite',
        'obs_colab',
        'obs_cord',
        'total_horas_trabalhadas',
        'status',
        'tipo',
        'tipo_ponto_id',
        'controlo_user_mes_id',
        'was_folga',
    ];

    public function utilizador()
    {
        return $this->belongsTo('App\Utilizador', 'utilizador_id');
    }

    public function tipo_ponto()
    {
        return $this->belongsTo('App\TipoPonto', 'tipo_ponto_id');
    }

    public function controlo_user_mes()
    {
        return $this->belongsTo('App\ControloRHUtilizadorMes', 'controlo_user_mes_id');
    }

    public function is_folga()
    {
        return $this->tipo_ponto->id == 2;
    }

    public function get_abreviatura_tipo()
    {
        return "T";
    }

    public function get_tipo_for_view()
    {
        return "ponto";
    }
}
