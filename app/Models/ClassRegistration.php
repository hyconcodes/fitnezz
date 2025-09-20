<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassRegistration extends Model
{
    protected $fillable = [
        'class_id',
        'student_id',
        'status'
    ];
}
