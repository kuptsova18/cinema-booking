<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ShowTime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $dates = collect(range(0, 6))
            ->map(function (int $day): Carbon {
                return today()->copy()->addDays($day);
            });

        /*
         * Получаем дату из адресной строки.
         * Например: /?date=2026-07-08
         */
        $requestedDate = $request->string('date')->toString();

        /*
         * Разрешаем выбирать только дату
         * из подготовленного списка.
         */
        $availableDates = $dates
            ->map(function (Carbon $date): string {
                return $date->format('Y-m-d');
            })
            ->all();

        if (in_array($requestedDate, $availableDates, true)) {
            $selectedDate = Carbon::parse($requestedDate);
        } else {
            $selectedDate = today();
        }

        $showtimes = ShowTime::query()->with(['movie', 'hall'])
            ->whereHas('hall', function ($query): void {
                $query->where('is_active', true);
            })->whereBetween('starts_at', [
                    $selectedDate->copy()->startOfDay(),
                    $selectedDate->copy()->endOfDay(),
                ])->orderBy('starts_at')
            ->get();

        $showtimesByMovie = $showtimes->groupBy('movie_id');

        return view('client.schedule', [
            'dates' => $dates,
            'selectedDate' => $selectedDate,
            'showtimesByMovie' => $showtimesByMovie,
        ]);
    }
}
