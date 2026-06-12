<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolarCalculationItem extends Model
{
    protected $fillable = [
        'solar_calculation_id',
        'appliance_id',
        'watts',
        'quantity',
        'type',
    ];

    public function calculation()
    {
        return $this->belongsTo(SolarCalculation::class);
    }

    public function appliance()
    {
        return $this->belongsTo(Appliance::class);
    }
}
