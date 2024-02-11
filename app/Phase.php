<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Phase extends Model
{
    protected $fillable = [
        'phase'
    ];

    public function siembras(){
        return $this->hasMany(Siembra::class, 'phase_id')->withPivot('phase');
    }
}
