<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    protected $fillable = [
        'provider',
        'nit',
        'tel',
        'address',
        'state'
    ];
}
