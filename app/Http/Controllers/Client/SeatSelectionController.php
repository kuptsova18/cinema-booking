<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ShowTime;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use App\Models\Ticket;

class SeatSelectionController extends Controller
{
    public function show(ShowTime $showtime): View
    {
        $showtime->load(['movie', 'hall.seats',]);

        abort_unless($showtime->hall->is_active, 404);

        $seats = $showtime->hall->seats->sortBy(
            function ($seat): string {
                return sprintf(
                    '%04d-%04d',
                    $seat->row_number,
                    $seat->seat_number
                );
            }
        );

        $seatsByRow = $seats->groupBy('row_number');

        $occupiedSeatIds = Ticket::query()->where('showtime_id',$showtime->id)->pluck('seat_id');

        /*
         * Занятые места добавим после создания билетов.
         */
        return view(
            'client.seats',
            [
                'showtime' => $showtime,
                'seatsByRow' => $seatsByRow,
                'occupiedSeatIds' => $occupiedSeatIds,
            ]
        );

    }
}
