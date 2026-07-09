<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Seat;
use App\Models\ShowTime;
use App\Models\Ticket;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;

class BookingController extends Controller
{
    public function store(
        Request $request,
        ShowTime $showtime
    ): RedirectResponse {
        $validated = $request->validate([
            'seat_ids' => [
                'required',
                'string',
            ],
        ]);

        $showtime->load([
            'hall',
            'movie',
        ]);

        abort_unless(
            $showtime->hall->is_active,
            404
        );

        abort_if(
            $showtime->starts_at->isPast(),
            404
        );

        /*
         * Преобразуем строку "1,2,3"
         * в коллекцию идентификаторов.
         */
        $seatIds = collect(
            explode(',', $validated['seat_ids'])
        )
            ->map(
                fn(string $seatId): int =>
                (int) trim($seatId)
            )
            ->filter(
                fn(int $seatId): bool =>
                $seatId > 0
            )
            ->unique()
            ->values();

        if ($seatIds->isEmpty()) {
            throw ValidationException::withMessages([
                'seat_ids' => 'Выберите хотя бы одно место.',
            ]);
        }

        /*
         * Получаем только места нужного зала.
         */
        $seats = Seat::query()
            ->where(
                'hall_id',
                $showtime->hall_id
            )
            ->whereIn('id', $seatIds)
            ->get();

        if ($seats->count() !== $seatIds->count()) {
            throw ValidationException::withMessages([
                'seat_ids' =>
                    'Некоторые выбранные места не принадлежат этому залу.',
            ]);
        }

        if (
            $seats->contains(
                fn(Seat $seat): bool =>
                $seat->type === 'disabled'
            )
        ) {
            throw ValidationException::withMessages([
                'seat_ids' =>
                    'Недоступное место нельзя забронировать.',
            ]);
        }

        try {
            $booking = DB::transaction(
                function () use ($showtime, $seats, $seatIds): Booking {
                    /*
                     * Проверяем, не забронированы ли места.
                     */
                    $occupiedSeatIds = Ticket::query()
                        ->where(
                            'showtime_id',
                            $showtime->id
                        )
                        ->whereIn(
                            'seat_id',
                            $seatIds
                        )
                        ->pluck('seat_id');

                    if ($occupiedSeatIds->isNotEmpty()) {
                        throw ValidationException::withMessages([
                            'seat_ids' =>
                                'Некоторые места уже заняты. Выберите другие.',
                        ]);
                    }

                    $totalPrice = $seats->sum(
                        function (Seat $seat) use ($showtime): float {
                            return $seat->type === 'vip'
                                ? (float) $showtime
                                    ->hall
                                    ->vip_price
                                : (float) $showtime
                                    ->hall
                                    ->standard_price;
                        }
                    );

                    $booking = Booking::query()->create([
                        'showtime_id' => $showtime->id,
                        'code' => (string) Str::uuid(),
                        'total_price' => $totalPrice,
                        'status' => 'confirmed',
                    ]);

                    foreach ($seats as $seat) {
                        $price = $seat->type === 'vip'
                            ? $showtime->hall->vip_price
                            : $showtime->hall->standard_price;

                        Ticket::query()->create([
                            'booking_id' => $booking->id,
                            'showtime_id' => $showtime->id,
                            'seat_id' => $seat->id,
                            'code' => (string) Str::uuid(),
                            'price' => $price,
                        ]);
                    }

                    return $booking;
                }
            );
        } catch (QueryException) {
            return back()
                ->withInput()
                ->withErrors([
                    'seat_ids' =>
                        'Одно из выбранных мест уже забронировано.',
                ]);
        }

        return redirect()->route(
            'client.bookings.show',
            $booking
        );
    }

    public function show(Booking $booking): View
    {
        $booking->load([
            'showtime.movie',
            'showtime.hall',
            'tickets.seat',
        ]);

        $sortedTickets = $booking
            ->tickets
            ->sortBy(function (Ticket $ticket): string {
                return sprintf(
                    '%04d-%04d',
                    $ticket->seat->row_number,
                    $ticket->seat->seat_number
                );
            })
            ->values();

        $booking->setRelation(
            'tickets',
            $sortedTickets
        );

        $ticketUrl = route('client.bookings.verify', $booking);

        $writer = new SvgWriter();

        $qrCode = new QrCode(
            data: $ticketUrl,
            size: 300,
            margin: 10
        );

        $qrCodeResult = $writer->write($qrCode);

        return view('client.booking', [
            'booking' => $booking,
            'qrCodeDataUri' => $qrCodeResult->getDataUri(),
        ]);
    }

    public function verify(Booking $booking): View
    {
        $booking->load([
            'showtime.movie',
            'showtime.hall',
            'tickets.seat',
        ]);

        /*
         * Билет считаем действительным до завершения фильма.
         */
        $showtimeEndsAt = $booking
            ->showtime
            ->starts_at
            ->copy()
            ->addMinutes(
                $booking
                    ->showtime
                    ->movie
                    ->duration_minutes
            );

        if ($booking->status !== 'confirmed') {
            $verificationStatus = 'invalid';
            $verificationMessage = 'Бронирование недействительно.';
        } elseif (now()->greaterThan($showtimeEndsAt)) {
            $verificationStatus = 'expired';
            $verificationMessage = 'Сеанс уже завершён.';
        } else {
            $verificationStatus = 'valid';
            $verificationMessage = 'Билет действителен.';
        }

        return view('client.booking-verify', [
            'booking' => $booking,
            'verificationStatus' => $verificationStatus,
            'verificationMessage' => $verificationMessage,
        ]);
    }
}
