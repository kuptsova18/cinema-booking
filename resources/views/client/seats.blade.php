@extends('layouts.client')

@section(
    'title',
    'Выбор мест | ' . $showtime->movie->title
)

@section('content')

    <main>

        <section class="buying">

            <div class="buying__info">

                <div class="buying__info-description">

                    <h2 class="buying__info-title">
                        {{ $showtime->movie->title }}
                    </h2>

                    <p class="buying__info-start">
                        Начало сеанса:
                        {{ $showtime->starts_at->format('H:i') }}
                    </p>

                    <p class="buying__info-hall">
                        {{ $showtime->hall->name }}
                    </p>

                </div>

                <div class="buying__info-hint">

                    <p>
                        Тапните дважды,<br>
                        чтобы увеличить
                    </p>

                </div>

            </div>

            <div class="buying-scheme">

                <div class="buying-scheme__wrapper">

                    @foreach ($seatsByRow as $rowNumber => $row)

                        <div
                            class="buying-scheme__row"
                            aria-label="Ряд {{ $rowNumber }}"
                        >

                            @foreach ($row as $seat)

                                @php
                                    $isDisabled =
                                        $seat->type === 'disabled';

                                    $isOccupied =
                                        $occupiedSeatIds->contains(
                                            $seat->id
                                        );

                                    $price =
                                        $seat->type === 'vip'
                                            ? $showtime->hall->vip_price
                                            : $showtime->hall->standard_price;
                                @endphp

                                <button
                                    type="button"
                                    @class([
                                        'buying-scheme__chair',

                                        'buying-scheme__chair_disabled' =>
                                            $isDisabled,

                                        'buying-scheme__chair_taken' =>
                                            $isOccupied,

                                        'buying-scheme__chair_standart' =>
                                            !$isDisabled
                                            && !$isOccupied
                                            && $seat->type === 'standard',

                                        'buying-scheme__chair_vip' =>
                                            !$isDisabled
                                            && !$isOccupied
                                            && $seat->type === 'vip',
                                    ])
                                    data-seat-id="{{ $seat->id }}"
                                    data-seat-row="{{ $seat->row_number }}"
                                    data-seat-number="{{ $seat->seat_number }}"
                                    data-seat-price="{{ (float) $price }}"
                                    title="Ряд {{ $seat->row_number }}, место {{ $seat->seat_number }}"
                                    aria-label="Ряд {{ $seat->row_number }}, место {{ $seat->seat_number }}"
                                    @disabled(
                                        $isDisabled || $isOccupied
                                    )
                                ></button>

                            @endforeach

                        </div>

                    @endforeach

                </div>

                <div class="buying-scheme__legend">

                    <div class="col">

                        <p class="buying-scheme__legend-price">

                            <span
                                class="
                                    buying-scheme__chair
                                    buying-scheme__chair_standart
                                "
                            ></span>

                            Свободно
                            (
                            <span class="buying-scheme__legend-value">
                                {{ number_format(
                                    (float) $showtime->hall->standard_price,
                                    0,
                                    ',',
                                    ' '
                                ) }}
                            </span>
                            руб)

                        </p>

                        <p class="buying-scheme__legend-price">

                            <span
                                class="
                                    buying-scheme__chair
                                    buying-scheme__chair_vip
                                "
                            ></span>

                            Свободно VIP
                            (
                            <span class="buying-scheme__legend-value">
                                {{ number_format(
                                    (float) $showtime->hall->vip_price,
                                    0,
                                    ',',
                                    ' '
                                ) }}
                            </span>
                            руб)

                        </p>

                    </div>

                    <div class="col">

                        <p class="buying-scheme__legend-price">

                            <span
                                class="
                                    buying-scheme__chair
                                    buying-scheme__chair_taken
                                "
                            ></span>

                            Занято

                        </p>

                        <p class="buying-scheme__legend-price">

                            <span
                                class="
                                    buying-scheme__chair
                                    buying-scheme__chair_selected
                                "
                            ></span>

                            Выбрано

                        </p>

                    </div>

                </div>

            </div>

           <form
    action="{{ route(
        'client.bookings.store',
        $showtime
    ) }}"
    method="POST"
>
    @csrf

    <input
        type="hidden"
        name="seat_ids"
        value="{{ old('seat_ids') }}"
        data-selected-seat-ids
    >

    @error('seat_ids')

        <p class="booking-error">
            {{ $message }}
        </p>

    @enderror

    <button
        type="submit"
        class="acceptin-button"
        data-booking-button
        disabled
    >
        Забронировать
    </button>
</form>

        </section>

    </main>

@endsection

@push('scripts')

    <script
        src="{{ asset('client-assets/js/hall.js') }}"
        defer
    ></script>

@endpush