<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoPonto extends Model
{
    protected $table = 'tipo_ponto';

    public function ponto(){
        return $this->hasMany('App\Ponto', 'ponto_tipo_ponto_id');
    }
}
