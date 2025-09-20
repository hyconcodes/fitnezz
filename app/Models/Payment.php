<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'amount',
        'status',
        'reference',
        'payment_date'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime',
        'status' => 'string'
    ];

    /**
     * Get the student that owns the payment.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
