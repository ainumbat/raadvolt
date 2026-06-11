<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appliance extends Model
{
    /** @use HasFactory<\Database\Factories\ApplianceFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'watts',
        'type',
        'daily_runtime',
        'icon',
    ];
}
