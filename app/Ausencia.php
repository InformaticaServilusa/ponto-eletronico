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
        'controlo_user_mes_id',
    ];

    public function utilizador(){
        return $this->belongsTo('App\Utilizador', 'utilizador_id');
    }

    public function tipo_ausencia(){
        return $this->belongsTo('App\TiposAusencia', 'tipo_ausencia_id');
    }

    public function controlo_user_mes(){
        return $this->belongsTo('App\ControloRHUtilizadorMes', 'controlo_user_mes_id');
    }

    public function is_ferias(){
        return $this->tipo_ausencia_id == 2;
    }

    public function get_tipo_ausencia(){
        return $this->tipo_ausencia->descricao;
    }

    public function is_falta()
    {
        return ($this->tipo_ausencia_id != 2 && $this->tipo_ausencia_id != 1);
    }

    public function is_folga(){
        return $this->tipo_ausencia_id == 1;
    }

    public function get_abreviatura_tipo(){
        return $this->tipo_ausencia->abreviatura;
    }

    public function get_tipo_for_view(){
        return "ausencia";
    }
}
