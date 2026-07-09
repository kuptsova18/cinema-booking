<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'showtime_id',
        'seat_id',
        'code',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'booking_id' => 'integer',
            'showtime_id' => 'integer',
            'seat_id' => 'integer',
            'price' => 'decimal:2',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
    public function showtime(): BelongsTo
    {
        return $this->belongsTo(Showtime::class);
    }
    public function seat(): BelongsTo
    {
        return $this->belongsTo(Seat::class);
    }
}
