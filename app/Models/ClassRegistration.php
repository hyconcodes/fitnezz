<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassRegistration extends Model
{
    protected $table = 'class_registrations';

    protected $fillable = [
        'class_id',
        'student_id',
        'status',
        'progress',
        'comment',
        'workoutdiet',
    ];

    public function fitnessClass(): BelongsTo
    {
        return $this->belongsTo(FitnessClass::class, 'class_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    
}
