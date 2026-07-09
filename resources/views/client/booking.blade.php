@extends('layouts.client')

@section('title', 'Электронный билет | ИдёмВКино')

@section('content')

    <main>

        <section class="ticket">

            <header class="tichet__check">

                <h2 class="ticket__check-title">
                    Электронный билет
                </h2>

            </header>

            <div class="ticket__info-wrapper">

                <p class="ticket__info">
                    На фильм:

                    <span class="ticket__details">
                        {{ $booking->showtime->movie->title }}
                    </span>
                </p>

                <p class="ticket__info">
                    Места:

                    <span class="ticket__details">

                        @foreach ($booking->tickets as $ticket)

                            ряд {{ $ticket->seat->row_number }},
                            место {{ $ticket->seat->seat_number }}

                            @unless ($loop->last)
                                ;
                            @endunless

                        @endforeach

                    </span>
                </p>

                <p class="ticket__info">
                    В зале:

                    <span class="ticket__details">
                        {{ $booking->showtime->hall->name }}
                    </span>
                </p>

                <p class="ticket__info">
                    Начало сеанса:

                    <span class="ticket__details">
                        {{ $booking
                            ->showtime
                            ->starts_at
                            ->format('d.m.Y H:i') }}
                    </span>
                </p>

                <p class="ticket__info">
                    Стоимость:

                    <span class="ticket__details">
                        {{ number_format(
                            (float) $booking->total_price,
                            0,
                            ',',
                            ' '
                        ) }}
                        руб.
                    </span>
                </p>

                <p class="ticket__info">
                    Код бронирования:

                    <span class="ticket__details ticket__code">
                        {{ $booking->code }}
                    </span>
                </p>

                <img
                    class="ticket__info-qr"
                    src="{{ $qrCodeDataUri }}"
                    alt="QR-код электронного билета"
                >

                <p class="ticket__hint">
                    Покажите QR-код контролёру перед началом сеанса.
                </p>

                <p class="ticket__hint">
                    QR-код содержит адрес электронного билета.
                </p>

                <a
                    href="{{ route('client.schedule', [
                        'date' => $booking
                            ->showtime
                            ->starts_at
                            ->format('Y-m-d'),
                    ]) }}"
                    class="ticket__back-link"
                >
                    Вернуться к расписанию
                </a>

            </div>

        </section>

    </main>

@endsection