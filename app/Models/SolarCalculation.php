<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SolarCalculation extends Model
{
    protected $fillable = [
        'name',
        'email',
        'whatsapp',
        'total_load',
        'essential_load',
        'generator_load',
        'required_solar',
        'required_battery',
        'wire_length',
        'backup_hours',
        'report_data',
    ];

    public function items()
    {
        return $this->hasMany(SolarCalculationItem::class);
    }

    protected $appends = ['formatted_date'];

    public function getFormattedDateAttribute()
    {
        return Carbon::parse($this->created_at)
            ->format('d-M-Y h:i A');
    }
}
