<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ControloRHUtilizadorMes extends Model
{
    protected $table = 'controlo_user_mes';
    protected $fillable = [
        'utilizador_id',
        'ano_mes',
        'horas_trabalhadas',
        'ferias',
        'feriados_trabalhados',
        'horas_ausencia',
        '_processado',
    ];

    public function rules(){
        return [
            'utilizador_id' => 'required|integer|exists:utilizador, id',
            'ano_mes' => 'required|date_format:Y-m|unique:controlo_user_mes,utilizador_id, ano_mes',
        ];
    }

    public function utilizador(){
        return $this->belongsTo('App\Utilizador', 'utilizador_id');
    }

    public function ponto(){
        return $this->hasMany('App\Ponto');
    }

    public function ausencia(){
        return $this->hasMany('App\Ausencia');
    }


}
