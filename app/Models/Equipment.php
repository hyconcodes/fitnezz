<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $fillable = [
        'name',
        'description',
        'status',
        'maintenance_schedule',
        'maintenance_notes',
    ];

    protected function casts(): array
    {
        return [
            'maintenance_schedule' => 'date',
        ];
    }
}
