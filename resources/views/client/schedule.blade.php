@extends('layouts.client')

@section('title', 'Расписание | ИдёмВКино')

@section('content')

    <nav class="page-nav">

        @foreach ($dates as $date)

            @php
                $isToday = $date->isToday();
                $isChosen = $selectedDate->isSameDay($date);
                $isWeekend = $date->isWeekend();

                $weekDay = mb_convert_case(
                    $date->locale('ru')->translatedFormat('D'),
                    MB_CASE_TITLE,
                    'UTF-8'
                );
            @endphp

            <a href="{{ route('client.schedule', [
                'date' => $date->format('Y-m-d'),
            ]) }}" @class([
                'page-nav__day',
                'page-nav__day_today' => $isToday,
                'page-nav__day_chosen' => $isChosen,
                'page-nav__day_weekend' => $isWeekend,
            ])>
                <span class="page-nav__day-week">
                    {{ $weekDay }}
                </span>

                <span class="page-nav__day-number">
                    {{ $date->format('j') }}
                </span>
            </a>

        @endforeach

        <a href="{{ route('client.schedule', [
        'date' => $dates
            ->last()
            ->copy()
            ->addDay()
            ->format('Y-m-d'),
    ]) }}" class="page-nav__day page-nav__day_next" aria-label="Показать следующие даты"></a>

    </nav>

    <main>

        @if ($showtimesByMovie->isEmpty())

            <section class="movie">

                <h2 class="movie__title">
                    Сеансов нет
                </h2>

                <p class="movie__synopsis">
                    На выбранную дату сеансы пока не добавлены.
                </p>

            </section>

        @else

            @foreach ($showtimesByMovie as $movieShowtimes)

                @php
                    $firstShowtime = $movieShowtimes->first();

                    $movie = $firstShowtime->movie;

                    $showtimesByHall = $movieShowtimes
                        ->sortBy('starts_at')
                        ->groupBy('hall_id');
                @endphp

                <section class="movie">

                    <div class="movie__info">

                        <div class="movie__poster">

                            @if ($movie->poster_path)

                                    <img class="movie__poster-image" alt="{{ $movie->title }} постер" src="{{ asset(
                                    'storage/' . $movie->poster_path
                                ) }}">

                            @else

                                <div class="movie__poster-image movie__poster-image_empty">
                                    Нет постера
                                </div>

                            @endif

                        </div>

                        <div class="movie__description">

                            <h2 class="movie__title">
                                {{ $movie->title }}
                            </h2>

                            <p class="movie__synopsis">
                                {{ $movie->description }}
                            </p>

                            <p class="movie__data">

                                <span class="movie__data-duration">
                                    {{ $movie->duration_minutes }} минут
                                </span>

                                <span class="movie__data-origin">
                                    {{ $movie->country }}
                                </span>

                            </p>

                        </div>

                    </div>

                    @foreach ($showtimesByHall as $hallShowtimes)

                        @php
                            $hall = $hallShowtimes->first()->hall;
                        @endphp

                        <div class="movie-seances__hall">

                            <h3 class="movie-seances__hall-title">
                                {{ $hall->name }}
                            </h3>

                            <ul class="movie-seances__list">

                                @foreach ($hallShowtimes as $showtime)

                                    <li class="movie-seances__time-block">

                                        <a class="movie-seances__time" href="{{ route(
                                        'client.showtimes.seats',
                                        $showtime
                                    ) }}">
                                            {{ $showtime
                                        ->starts_at
                                        ->format('H:i') }}
                                        </a>

                                    </li>

                                @endforeach

                            </ul>

                        </div>

                    @endforeach

                </section>

            @endforeach

        @endif

    </main>

@endsection