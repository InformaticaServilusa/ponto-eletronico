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
    ];

    public function usuario(){
        return $this->belongsTo('App\Usuario', 'usuario_id');
    }
}
