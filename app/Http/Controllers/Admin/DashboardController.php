<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hall;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Movie;
use App\Models\ShowTime;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    // класс вызываемый как функция
    public function __invoke(Request $request): View
    {
        /*
         * Получаем список залов для кнопок выбора.
         */
        $halls = Hall::query()
            ->orderBy('id')
            ->get();

        /*
         * По умолчанию зал не выбран.
         */
        $selectedHall = null;

        if ($request->filled('hall')) {
            $selectedHall = Hall::query()
                ->with([
                    'seats' => function ($query) {
                        $query
                            ->orderBy('row_number')
                            ->orderBy('seat_number');
                    },
                ])
                ->find($request->integer('hall'));
        }

        $movies = Movie::query()->orderByDesc('id')->get();
        $showtimes = ShowTime::query()->with('hall','movie')->orderBy('starts_at')->get();
        return view('admin.index', [
            'halls' => $halls,
            'selectedHall' => $selectedHall,
            'movies' => $movies,
            'showtimes' => $showtimes,
        ]);
    }
}
