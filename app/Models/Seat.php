<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seat extends Model
{
    use HasFactory;

    public const TYPE_STANDARD = 'standard';
    public const TYPE_VIP = 'vip';
    public const TYPE_DISABLED = 'disabled';

    protected $fillable = [
        'hall_id',
        'row_number',
        'seat_number',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'hall_id' => 'integer',
            'row_number'=> 'integer',
            'seat_number'=> 'integer',
        ];
    }

    public function hall(): BelongsTo 
    {
        return $this->BelongsTo(Hall::class);
    }
    
    public function tickets(): HasMany 
    {
        return $this->HasMany(Ticket::class);
    }
}


