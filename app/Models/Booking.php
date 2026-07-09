<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'showtime_id',
        'code',
        'customer_name',
        'customer_email',
        'total_price',
        'status',
    ];


    protected function casts(): array
    {
        return [
            'showtime_id' => 'integer',
            'total_price' => 'decimal:2',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'code';
    }
    public function showtime(): BelongsTo
    {
        return $this->belongsTo(Showtime::class);
    }
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}
