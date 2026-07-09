<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShowTime extends Model
{
    use HasFactory;
    protected $table = 'showtimes';

    protected $fillable = [
        'hall_id',
        'movie_id',
        'starts_at'
    ];


    protected function casts(): array
    {
        return [
            'hall_id'=>'integer',
            'movie_id'=>'integer',
            'starts_at'=>'datetime'
        ];
    }

    public function hall(): BelongsTo
    {
        return $this->belongsTo(Hall::class);
    }

    public function movie(): BelongsTo
    {
        return $this->belongsTo(Movie::class);
    }
    public function bookings(): HasMany
    {
        return $this->HasMany(Booking::class);
    }
    public function tickets(): HasMany
    {
        return $this->HasMany(Ticket::class);
    }
}
