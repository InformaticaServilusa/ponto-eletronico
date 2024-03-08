<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    protected $table = 'horario';
    public function regime()
    {
        return $this->belongsTo('App\Regime', 'regime_id');
    }
}
