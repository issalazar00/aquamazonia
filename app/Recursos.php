<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Recursos extends Model
{
    protected $table = 'recursos';

    protected $fillable = [
        
        'costo',
        'recurso',
        'unidad',
        'warehouse_id',
        'category_id',
        'brand_id',
        'provider_id'
    ];

    protected $with = [
        'warehouse',
        'provider',
        'category',
        'brand'
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }

    public function category()
    {
        return $this->belongsTo(ResourceCategory::class, 'category_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
}
