<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarView extends Model
{
    use HasFactory;

    protected $fillable = [
        'car_id',
        'ip_address',
        'user_agent',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }
}
