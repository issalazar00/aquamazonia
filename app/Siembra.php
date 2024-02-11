<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Siembra extends Model
{
    //
    protected $table = 'siembras';

    protected $fillable = [
        'id_contenedor',
        'nombre_siembra',
        'fecha_inicio',
        'fecha_alimento',
        'estado',
        'ini_descanso',
        'fin_descanso',
        'phase_id'
    ];

    protected $with =[
      'phase'
    ];

    public function phase(){
        return $this->belongsTo(Phase::class, 'phase_id');
    }

    public function peces() {
        return $this->hasMany(EspecieSiembra::class, 'id_siembra');
    }
}
