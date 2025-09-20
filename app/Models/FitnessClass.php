<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FitnessClass extends Model
{
    protected $table = 'classes';
    
    protected $fillable = [
        'title',
        'description',
        'trainer_id',
        'schedule_time',
        'capacity',
        'status'
    ];

    protected $casts = [
        'schedule_time' => 'datetime',
    ];
}
