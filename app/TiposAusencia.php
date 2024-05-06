<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TiposAusencia extends Model
{
    protected $table = 'tipo_ausencia';

    public $timestamps = false;

    public function ausencia()
    {
        return $this->hasMany('App\Ausencia', 'ausencia_tipo_ausencia_id');
    }
}
