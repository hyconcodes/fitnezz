<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ClassModel extends Model
{
    protected $table = 'classes';

    protected $fillable = [
        'trainer_id',
        'title',
        'description',
        'schedule_time',
        'capacity',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'schedule_time' => 'datetime',
        ];
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'class_bookings');
    }
}
