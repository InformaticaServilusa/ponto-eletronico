<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Utilizador extends Model
{
    protected $table = 'utilizador';
    protected $horas_mes = 0;
    protected $faltas_mes = 0;
    protected $guarded = [];
    protected $fillable = [
        'admin',
        'ativo',
        'cargo',
        'coordenador',
        'coordenador_id',
        'created_at',
        'departamento',
        'dep_rh',
        'email',
        'guuID',
        'local',
        'nome',
        'regime',
        'updated_at',

    ];
    public function pontoAjuste(){
    	return $this->hasMany('App\PontoAjuste');
    }

    public function ponto(){
    	return $this->hasMany('App\Ponto');
    }

    public function coordenador(){
        return $this->belongsTo('App\Utilizador', 'coordenador_id');
    }

    public function regime(){
        return $this->hasOne('App\Regime', 'regime');
    }

    public function ausencia()
    {
        return $this->hasMany('App\Ausencia');
    }

    public function setHorasMes($horas_mes){
        $this->horas_mes = $horas_mes;
    }

    public function getHorasMes(){
        return $this->horas_mes;
    }

    public function setFaltasMes($numero_faltas)
    {
        $this->faltas_mes = $numero_faltas;
    }

    public function getFaltasMes()
    {
        return $this->faltas_mes;
    }
}