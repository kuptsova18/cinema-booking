<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\ShowTime;
use SessionHandler;

class Hall extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'rows_count',
        'seats_per_row',
        'standard_price',
        'vip_price',
        'is_active'
    ];


    protected function casts(): array
    {
        return [
            'rows_count' => 'integer',
            'seats_per_row' => 'integer',
            'standard_price' => 'decimal:2',
            'vip_price' => 'decimal:2',
            'is_active' => 'boolean'
        ];
    }

    /**
     * Все места этого зала.
     */
    public function seats(): HasMany
    {
        return $this->hasMany(Seat::class);
    }

    /**
     * Все места этого зала.
     */
    public function showtimes(): HasMany
    {
        return $this->hasMany(ShowTime::class,'hall_id');
    }

}
