<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RecursoNecesario extends Model{
  protected $table = 'recursos_necesarios';

    protected $fillable = [
        'id_recurso',
        'id_alimento',
        'tipo_actividad',
        'fecha_ra',
        'horas_hombre',
        'minutos_hombre',
        'cantidad_recurso',
        'cant_manana',
        'cant_tarde',
        'conv_alimenticia',
        'detalles',
        'costo_minutos_hombre',
        'costo_recurso',
        'costo_alimento'
    ];
}