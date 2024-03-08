<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Utilizador extends Model
{
    protected $table = 'utilizador';
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
}
