<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Equipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'picture',
        'status',
        'maintenance_schedule',
        'last_serviced_at',
        'notes'
    ];

    protected $casts = [
        'maintenance_schedule' => 'date',
        'last_serviced_at' => 'datetime',
    ];
}
