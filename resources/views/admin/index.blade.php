@extends('layouts.admin')

@section('title', 'Панель администратора | ИдёмВКино')

@section('content')

    <main class="conf-steps">

        {{-- Управление залами --}}
        <section class="conf-step">

            <header class="conf-step__header conf-step__header_opened">

                <h2 class="conf-step__title">
                    Управление залами
                </h2>

            </header>

            <div class="conf-step__wrapper">
                @if (session('success'))

                    <p class="conf-step__wrapper__save-status">
                        {{ session('success') }}
                    </p>

                @endif

                @if (session('error'))

                    <p class="explanation-text">
                        {{ session('error') }}
                    </p>

                @endif

                <p class="conf-step__paragraph">
                    Доступные залы:
                </p>

                <ul class="conf-step__list">

                    @forelse ($halls as $hall)
                        <li>
                            {{ $hall->name }}
                            <button type="button" class="conf-step__button conf-step__button-trash"
                                data-popup-open="remove-hall-popup-{{ $hall->id }}" aria-label="Удалить зал {{ $hall->name }}"
                                title="Удалить зал"></button>
                        </li>

                    @empty
                        <li>
                            Залы пока не созданы
                        </li>
                    @endforelse

                </ul>

                <button type="button" class="conf-step__button conf-step__button-accent" data-popup-open="add-hall-popup">
                    Создать зал
                </button>

            </div>

        </section>


        {{-- Конфигурация залов --}}
        <section class="conf-step">

            <header class="conf-step__header conf-step__header_opened">

                <h2 class="conf-step__title">
                    Конфигурация залов
                </h2>

            </header>

            <div class="conf-step__wrapper">
                @if($halls->isEmpty())
                    <p class="conf-step__paragraph">
                        Сначала создайте хотя бы один зал.
                    </p>
                @else
                    <p class="conf-step__paragraph">
                        Выберите зал для конфигурации:
                    </p>

                    <form action="{{ route('admin.dashboard') }}" method="GET">
                        <ul class="conf-step__selectors-box">

                            @foreach ($halls as $hall)

                                <li>
                                    <input type="radio" class="conf-step__radio" name="hall" id="configuration-hall-{{ $hall->id }}"
                                        value="{{ $hall->id }}"
                                        onchange=" sessionStorage.setItem('adminScrollPosition', window.scrollY);
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        this.form.submit();"
                                        @checked($selectedHall?->id === $hall->id)>

                                    <label class="conf-step__selector" for="price-hall-{{ $hall->id }}">
                                        {{ $hall->name }}
                                    </label>
                                </li>

                            @endforeach

                        </ul>
                    </form>

                    @if (!$selectedHall)

                        <p class="conf-step__paragraph">
                            Нажмите на название зала, чтобы открыть его конфигурацию.
                        </p>

                    @else
                        @php
                            $useOldValues = (int) old('configuration_hall_id') === (int) $selectedHall->id;

                            $rowsCountValue = $useOldValues
                                ? old('rows_count')
                                : ($selectedHall->rows_count ?: 1);

                            $seatsPerRowValue = $useOldValues
                                ? old('seats_per_row')
                                : ($selectedHall->seats_per_row ?: 1);

                            $seatTypes = $selectedHall->seats->mapWithKeys(
                                function ($seat) {
                                    return [$seat->row_number . '-' . $seat->seat_number => $seat->type,];
                                }
                            );
                        @endphp

                        <form action="{{ route('admin.halls.configuration.update', $selectedHall) }}" method="POST"
                            data-hall-configuration-form autocomplete="off">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="configuration_hall_id" value="{{ $selectedHall->id }}">

                            <input type="hidden" name="seat_types" value="{{ $seatTypes->toJson() }}" data-seat-types-input>

                            <p class="conf-step__paragraph">
                                Настройка зала:
                                <strong>{{ $selectedHall->name }}</strong>
                            </p>

                            <p class="conf-step__paragraph">
                                Укажите количество рядов и максимальное количество кресел в ряду:
                            </p>

                            <div class="conf-step__legend">
                                <label class="conf-step__label">
                                    Рядов, шт.

                                    <input type="number" class="conf-step__input" name="rows_count" min="1" max="20"
                                        value="{{ $rowsCountValue }}" required>
                                </label>

                                <span class="multiplier">×</span>

                                <label class="conf-step__label">
                                    Мест, шт.

                                    <input type="number" class="conf-step__input" name="seats_per_row" min="1" max="30"
                                        value="{{ $seatsPerRowValue }}" required>
                                </label>
                            </div>
                            @error('rows_count', 'hallConfiguration')
                                <p class="explanation-text">
                                    {{ $message }}
                                </p>
                            @enderror

                            @error('seats_per_row', 'hallConfiguration')
                                <p class="explanation-text">
                                    {{ $message }}
                                </p>
                            @enderror

                            <p class="conf-step__paragraph"> Нажмите на кресло, чтобы изменить его тип:</p>
                            <p class="conf-step__legend">
                                <span class="conf-step__chair conf-step__chair_standard"></span>
                                — обычное кресло

                                <span class="conf-step__chair conf-step__chair_vip"></span>
                                — VIP-кресло

                                <span class="conf-step__chair conf-step__chair_disabled"></span>
                                — недоступное кресло
                            </p>
                            <div class="conf-step__hall">
                                <div class="conf-step__hall-wrapper">
                                    @if ($selectedHall->seats->isEmpty())
                                        <p class="conf-step__paragraph">
                                            Схема кресел ещё не создана.
                                            Укажите размеры зала и нажмите «Сохранить».
                                        </p>
                                    @else
                                        @foreach ($selectedHall->seats->groupBy('row_number') as $row)

                                            <div class="conf-step__row">

                                                @foreach ($row as $seat)

                                                    <button type="button"
                                                        class="conf-step__chair
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                @if ($seat->type === 'vip')
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    conf-step__chair_vip
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                @elseif ($seat->type === 'disabled')
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    conf-step__chair_disabled
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                @else
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    conf-step__chair_standart
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                @endif"
                                                        data-seat-key="{{ $seat->row_number }}-{{ $seat->seat_number }}"
                                                        data-seat-type="{{ $seat->type }}"
                                                        title="Ряд {{ $seat->row_number }}, место {{ $seat->seat_number }}"
                                                        aria-label="Ряд {{ $seat->row_number }}, место {{ $seat->seat_number }}"></button>

                                                @endforeach

                                            </div>

                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <div class="conf-step__buttons text-center">
                                <button type="submit" class="conf-step__button conf-step__button-accent">
                                    Сохранить
                                </button>
                            </div>
                        </form>
                    @endif
                @endif

            </div>

        </section>


        {{-- Конфигурация цен --}}
        <section class="conf-step" id="hall-prices">

            <header class="conf-step__header conf-step__header_opened">

                <h2 class="conf-step__title">
                    Конфигурация цен
                </h2>

            </header>

            <div class="conf-step__wrapper">

                @if ($halls->isEmpty())
                    <p class="conf-step__paragraph">
                        Сначала создайте хотя бы один зал.
                    </p>
                @else
                    <p class="conf-step__paragraph">
                        Выберите зал для конфигурации цен:
                    </p>

                    <form action="{{ route('admin.dashboard') }}" method="get">
                        <ul class="conf-step__selectors-box">
                            @foreach ($halls as $hall)
                                <li>
                                    <input type="radio" class="conf-step__radio" name="hall" id="price-hall-{{ $hall->id }}"
                                        value="{{ $hall->id }}"
                                        onchange="
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    sessionStorage.setItem(
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        'adminScrollPosition',
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        window.scrollY
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    );
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    this.form.submit();
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                "
                                        @checked($selectedHall?->id === $hall->id)>

                                    <label class="conf-step__selector" for="price=hall-{{ $hall->id }}"> {{ $hall->name }}</label>
                                </li>
                            @endforeach
                        </ul>
                    </form>

                    @if (!$selectedHall)
                        <p class="conf-step__paragraph">
                            Выберите зал, чтобы настроить стоимость билетов.
                        </p>
                    @else
                        @php
                            /*
                             * Старые значения после ошибки валидации
                             * используем только для текущего зала.
                             */
                            $useOldPriceValues =
                                (int) old('price_hall_id') ===
                                (int) $selectedHall->id;

                            $standardPriceValue = $useOldPriceValues
                                ? old('standard_price')
                                : ($selectedHall->standard_price ?? 0);

                            $vipPriceValue = $useOldPriceValues
                                ? old('vip_price')
                                : ($selectedHall->vip_price ?? 0);
                        @endphp
                        @if (session('price_success'))

                            <p class="conf-step__wrapper__save-status">
                                {{ session('price_success') }}
                            </p>

                        @endif
                        <form action="{{ route('admin.halls.prices.update', $selectedHall) }}" method="POST" autocomplete="off"
                            onsubmit="
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                sessionStorage.setItem(
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    'adminScrollPosition',
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    window.scrollY
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                );
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            ">

                            @csrf
                            @method('PUT')
                            <input type="hidden" name="price_hall_id" value="{{ $selectedHall->id }}">
                            <p class="conf-step__paragraph">
                                Настройка цен для зала:
                                <strong>{{ $selectedHall->name }}</strong>
                            </p>

                            <p class="conf-step__paragraph">
                                Установите стоимость билетов:
                            </p>
                            <div class="conf-step__legend">
                                <label class="conf-step__label">
                                    <span class="conf-step__chair" conf-step__chair_standard></span>
                                    Обычное кресло, руб.
                                    <input type="number" class="conf-step__input" name="standard_price" min="0" max="1000000"
                                        step="0.01" value="{{ $standardPriceValue }}" autocomplete="off" required>
                                </label>
                            </div>
                            @error('standard_price', 'hallPrices')

                                <p class="explanation-text">
                                    {{ $message }}
                                </p>

                            @enderror

                            <div class="conf-step__legend">

                                <label class="conf-step__label">
                                    <span
                                        class="conf-step__chair
                                                                                                                                                                                                                                                                                                                                                                                                                                                        conf-step__chair_vip"></span>

                                    VIP-кресло, руб.

                                    <input type="number" class="conf-step__input" name="vip_price" min="0" max="1000000" step="0.01"
                                        value="{{ $vipPriceValue }}" autocomplete="off" required>
                                </label>

                            </div>

                            @error('vip_price', 'hallPrices')

                                <p class="explanation-text">
                                    {{ $message }}
                                </p>

                            @enderror

                            <div class="conf-step__buttons text-center">

                                <button type="submit"
                                    class="conf-step__button
                                                                                                                                                                                                                                                                                                                                                                                                                                        conf-step__button-accent">
                                    Сохранить
                                </button>

                            </div>
                        </form>

                    @endif
                @endif
            </div>

        </section>


        {{-- Сетка сеансов --}}
        <section class="conf-step" id="showtime-grid">

            <header class="conf-step__header conf-step__header_opened">

                <h2 class="conf-step__title">
                    Сетка сеансов
                </h2>

            </header>

            <div class="conf-step__wrapper">

                @if (session('movie_success'))
                    <p class="conf-step__wrapper__save-status">
                        {{ session('movie_success') }}
                    </p>
                @endif

                @if (session('movie_error'))
                    <p class="explanation-text">
                        {{ session('movie_error') }}
                    </p>
                @endif

                <p class="conf-step__paragraph">
                    Фильмы:
                </p>

                @if ($movies->isEmpty())
                    <p class="conf-step__paragraph">
                        Фильмы пока не добавлены.
                    </p>
                @else
                    <div class="conf-step__movies">
                        @foreach ($movies as $movie)
                            <article class="conf-step__movie">
                                @if($movie->poster_path)
                                    <img class="conf-step__movie-poster" src="{{ asset('storage/' . $movie->poster_path) }}"
                                        alt="Постер фильма {{ $movie->title }}">
                                @endif
                                <h3 class="conf-step__movie-title">
                                    {{ $movie->title }}
                                </h3>
                                <p class="conf-step__movie-duration">
                                    {{ $movie->duration_minutes }} минут
                                </p>

                                <p class="conf-step__movie-duration">
                                    {{ $movie->country }}
                                </p>
                                <button type="button"
                                    class="conf-step__button
                                                                                                                                                                                                                                                                                                                                                           conf-step__button-trash
                                                                                                                                                                                                                                                                                                                                                           conf-step__movie-delete"
                                    data-popup-open="remove-movie-popup-{{ $movie->id }}"
                                    aria-label="Удалить фильм {{ $movie->title }}" title="Удалить фильм"></button>
                            </article>
                        @endforeach
                    </div>
                @endif

                <button type="button"
                    class="conf-step__button
                                                                                                                       conf-step__button-accent"
                    data-popup-open="add-movie-popup">
                    Добавить фильм
                </button>

                @if($halls->isNotEmpty() && $movies->isNotEmpty())
                    <button type="button"
                        class="conf-step__button
                                                                                                                                                                                                                       conf-step__button-accent"
                        data-popup-open="add-showtime-popup">
                        Добавить сеанс
                    </button>
                @else
                    <p class="conf-step__paragraph explanation-text">
                        Чтобы добавить сеанс, сначала создайте зал и фильм.
                    </p>
                @endif

                @if (session('showtime_success'))
                    <p class="conf-step__wrapper__save-status">
                        {{ session('showtime_success') }}
                    </p>
                @endif

                @if(session('showtime_error'))
                    <p class="explanation-text">
                        {{ session('showtime_error') }}
                    </p>
                @endif

                <p class="conf-step__paragraph">
                    Расписание сеансов:
                </p>

                @if($showtimes->isEmpty())
                    <p class="conf-step__paragraph">
                        Сеансы пока не добавлены.
                    </p>
                @else
                    @foreach ($halls as $hall)
                        @php
                            $hallShowTimes = $showtimes->where('hall_id', $hall->id);
                            $showtimesByDate = $hallShowTimes->groupBy(
                                function ($showtime) {
                                    return $showtime->starts_at->format('Y-m-d');
                                }
                            );
                        @endphp

                        <div class="conf-step__seances-hall">
                            <h3 class="conf-step__seances-title">
                                {{ $hall->name }}
                            </h3>

                            @if($hallShowTimes->isEmpty())
                                <p class="conf-step__paragraph">
                                    Для этого зала сеансы не созданы.
                                </p>
                            @else
                                @foreach ($showtimesByDate as $showtimeDate => $dateShowTimes)
                                    <p class="conf-step__paragraph">
                                        {{ \Carbon\Carbon::parse($showtimeDate)->format('d.m.Y')  }}
                                    </p>

                                    <div class="conf-step__seances-timeline">
                                        @foreach ($dateShowTimes as $showtime)
                                            @php
                                                $startMinutes =
                                                    $showtime->starts_at->hour
                                                    * 60
                                                    + $showtime->starts_at->minute;

                                                $movieWidth = max(
                                                    60,
                                                    $showtime
                                                        ->movie
                                                        ->duration_minutes
                                                    * 0.5
                                                );

                                                $leftPosition =
                                                    $startMinutes * 0.5;   
                                            @endphp

                                            <div class="conf-step__seances-movie"
                                                style="
                                                                                                                                                                                                                                                                                                                                                                            left: {{ $leftPosition }}px;
                                                                                                                                                                                                                                                                                                                                                                            width: {{ $movieWidth }}px;
                                                                                                                                                                                                                                                                                                                                                                        "
                                                title="
                                                                                                                                                                                                                                                                                                                                                                            {{ $showtime->movie->title }},
                                                                                                                                                                                                                                                                                                                                                                            {{ $showtime->starts_at->format('H:i') }}
                                                                                                                                                                                                                                                                                                                                                                        ">
                                                <p class="conf-step__seances-movie-title">
                                                    {{ $showtime->movie->title }}
                                                </p>
                                                <p class="conf-step__seances-movie-start">
                                                    {{ $showtime->starts_at->format('H:i')}}
                                                </p>
                                                <button type="button" class="trash-seance"
                                                    data-popup-open="remove-showtime-popup-{{ $showtime->id }}" aria-label="Удалить сеанс"
                                                    title="Удалить сеанс">
                                                    ×
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    @endforeach
                @endif
            </div>

        </section>


        {{-- Открытие продаж --}}
        <section class="conf-step" id="hall-sales">

            <header class="conf-step__header conf-step__header_opened">

                <h2 class="conf-step__title">
                    Открыть продажи
                </h2>

            </header>

            <div class="conf-step__wrapper text-center">

                @if (session('sales_success'))

                    <p class="conf-step__wrapper__save-status">
                        {{ session('sales_success') }}
                    </p>

                @endif

                @if (session('sales_error'))

                    <p class="explanation-text">
                        {{ session('sales_error') }}
                    </p>

                @endif

                @if ($halls->isEmpty())

                    <p class="conf-step__paragraph">
                        Сначала создайте хотя бы один зал.
                    </p>

                @else
                    <p class="conf-step__paragraph">
                        Откройте или закройте продажу билетов для выбранного зала.
                    </p>
                @endif
                @foreach ($halls as $hall)

                            <div class="conf-step__sales-hall">

                                <p class="conf-step__paragraph">
                                    Зал:
                                    <strong>{{ $hall->name }}</strong>
                                </p>

                                <p class="conf-step__paragraph">
                                    Статус:

                                    @if ($hall->is_active)

                                        <strong class="sales-status sales-status_open">
                                            продажи открыты
                                        </strong>

                                    @else

                                        <strong class="sales-status sales-status_closed">
                                            продажи закрыты
                                        </strong>

                                    @endif
                                </p>

                                <form action="{{ route('admin.halls.sales.update',$hall) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <button type="submit" class="conf-step__button conf-step__button-accent">
                                        @if ($hall->is_active)
                                            Закрыть продажу билетов
                                        @else
                                            Открыть продажу билетов
                                        @endif
                                    </button>

                                </form>

                            </div>

                @endforeach

            </div>

        </section>


        {{-- Выход --}}
        <section class="conf-step">

            <header class="conf-step__header conf-step__header_opened">

                <h2 class="conf-step__title">
                    Выход
                </h2>

            </header>

            <div class="conf-step__wrapper text-center">

                <p class="conf-step__paragraph">
                    Вы вошли как:
                    <strong>{{ auth()->user()->email }}</strong>
                </p>

                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf

                    <button type="submit" class="conf-step__button conf-step__button-regular">
                        Выйти
                    </button>

                </form>

            </div>

        </section>

    </main>

    {{-- Модальное окно добавления зала --}}
    @include('admin.popups.add-hall')
    @include('admin.popups.add-movie')
    @include('admin.popups.add-showtime')

    @foreach ($halls as $hall)

        @include('admin.popups.remove-hall', [
            'hall' => $hall,
        ])
    @endforeach
    @foreach ($movies as $movie)
        @include('admin.popups.remove-movie', [
            'movie' => $movie,
        ])
    @endforeach
    @foreach ($showtimes as $showtime)

        @include('admin.popups.remove-showtime', [
            'showtime' => $showtime,
        ])

    @endforeach

@endsection