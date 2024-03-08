<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Regime extends Model
{
    protected $table = 'regime';
    public function horario()
    {
        return $this->hasMany('App\Horario', 'regime_id');
    }
}
