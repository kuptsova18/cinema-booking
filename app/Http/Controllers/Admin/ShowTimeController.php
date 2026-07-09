<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hall;
use App\Models\Movie;
use App\Models\ShowTime;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ShowTimeController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validateWithBag(
            'showtimeCreation',
            [
                'hall_id' => [
                    'required',
                    'integer',
                    'exists:halls,id',
                ],

                'movie_id' => [
                    'required',
                    'integer',
                    'exists:movies,id',
                ],

                'starts_at' => [
                    'required',
                    'date',
                ],
            ],
            [
                'hall_id.required' =>
                    'Выберите зал.',

                'hall_id.exists' =>
                    'Выбранный зал не найден.',

                'movie_id.required' =>
                    'Выберите фильм.',

                'movie_id.exists' =>
                    'Выбранный фильм не найден.',

                'starts_at.required' =>
                    'Укажите дату и время начала сеанса.',

                'starts_at.date' =>
                    'Укажите корректную дату и время.',
            ]
        );

        $hall = Hall::query()->findOrFail($validated['hall_id']);

        $movie = Movie::query()->findOrFail($validated['movie_id']);

        $startsAt = Carbon::parse($validated['starts_at'])->seconds(0);

        $endtsAt = $startsAt->copy()->addMinutes($movie->duration_minutes);

        $hallShowTimes = ShowTime::query()->with('movie')->where('hall_id', $hall->id)->get();

        /*
         * Проверяем пересечение:
         *
         * новый сеанс начинается раньше окончания старого
         * и заканчивается позже начала старого.
         */
        $conflictingShowTime = $hallShowTimes->first(
            function (ShowTime $existingShowtime) use ($startsAt, $endtsAt): bool {
                $existingStartsAt = $existingShowtime->starts_at->copy();
                $existingEndsAt = $existingShowtime->starts_at->copy()->addMinutes($existingShowtime->movie->duration_minutes);

                return $startsAt->lt($existingEndsAt) && $endtsAt->gt($existingStartsAt);

            }
        );

        if ($conflictingShowTime) {
            $conflictingEndsAt = $conflictingShowTime->starts_at->copy()->addMinutes(($conflictingShowTime->movie->duration_minutes));
            return redirect()->to(route('admin.dashboard') . '#showtime-grid')->withErrors(
                [
                    'starts_at' =>
                        'Этот сеанс пересекается с фильмом «'
                        . $conflictingShowTime->movie->title
                        . '», который идёт с '
                        . $conflictingShowTime
                            ->starts_at
                            ->format('d.m.Y H:i')
                        . ' до '
                        . $conflictingEndsAt
                            ->format('H:i')
                        . '.',
                ],
                'showtimeCreation'
            )->withInput();
        }

        ShowTime::create([
            'hall_id' => $hall->id,
            'movie_id' => $movie->id,
            'starts_at' => $startsAt,
        ]);
        return redirect()
            ->to(
                route('admin.dashboard')
                . '#showtime-grid'
            )
            ->with(
                'showtime_success',
                'Сеанс успешно добавлен.'
            );
    }

    public function destroy(ShowTime $showtime): RedirectResponse
    {
        $movieTitle = $showtime->movie->title;

        try {
            $showtime->delete();
        } catch (QueryException) {
            return redirect()
                ->to(
                    route('admin.dashboard')
                    . '#showtime-grid'
                )
                ->with(
                    'showtime_error',
                    'Нельзя удалить сеанс, для которого уже оформлены бронирования или билеты.'
                );

        }
        
        return redirect()
            ->to(
                route('admin.dashboard')
                . '#showtime-grid'
            )
            ->with(
                'showtime_success',
                "Сеанс фильма «{$movieTitle}» удалён."
            );
    }
}
