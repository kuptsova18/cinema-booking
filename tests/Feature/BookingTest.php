<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Hall;
use App\Models\Movie;
use App\Models\Seat;
use App\Models\ShowTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_free_seat_can_be_booked(): void
    {
        [$showtime, $seat] = $this->createShowtimeAndSeat();

        $response = $this->post(
            route('client.bookings.store', $showtime),
            [
                'seat_ids' => (string) $seat->id,
            ]
        );
        $booking = Booking::query()->firstOrFail();

        $response->assertRedirect(
            route('client.bookings.show', $booking)
        );

        $this->assertDatabaseHas('tickets', [
            'booking_id' => $booking->id,
            'showtime_id' => $showtime->id,
            'seat_id' => $seat->id,
        ]);

    }

    private function createShowtimeAndSeat(): array
    {
        $hall = Hall::query()->create([
            'name' => 'Тестовый зал',
            'rows_count' => 1,
            'seats_per_row' => 1,
            'standard_price' => 300,
            'vip_price' => 500,
            'is_active' => true,
        ]);

        $seat = Seat::query()->create([
            'hall_id' => $hall->id,
            'row_number' => 1,
            'seat_number' => 1,
            'type' => 'standard',
        ]);

        $movie = Movie::query()->create([
            'title' => 'Тестовый фильм',
            'duration_minutes' => 120,
            'description' => 'Описание тестового фильма',
            'country' => 'Россия',
            'poster_path' => null,
        ]);

        $showtime = ShowTime::query()->create([
            'hall_id' => $hall->id,
            'movie_id' => $movie->id,
            'starts_at' => now()
                ->addDay()
                ->setTime(18, 30),
        ]);

        return [$showtime, $seat];
    }

    public function test_same_seat_cannot_be_booked_twice(): void
    {
        [$showtime, $seat] = $this->createShowtimeAndSeat();

        $this->post(
            route('client.bookings.store', $showtime),
            [
                'seat_ids' => (string) $seat->id,
            ]
        )->assertSessionHasNoErrors();

        $this->post(
            route('client.bookings.store', $showtime),
            [
                'seat_ids' => (string) $seat->id,
            ]
        )->assertSessionHasErrors('seat_ids');

        $this->assertDatabaseCount('bookings', 1);
        $this->assertDatabaseCount('tickets', 1);
    }
}
