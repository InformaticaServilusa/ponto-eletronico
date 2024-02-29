<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PontoAjusteRazao extends Model
{
    protected $table = 'ponto_ajuste_razao';
    public $timestamps = false;

    public function pontoRazao(){
    	return $this->hasMany('App\PontoAjuste');
    }

    public $incrementing = true;

    protected $fillable = ['id', 'descricao', 'ativo'];
}
