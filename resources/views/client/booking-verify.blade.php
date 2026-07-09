@extends('layouts.client')

@section('title', 'Проверка билета | ИдёмВКино')

@section('content')

    <main>

        <section class="ticket">

            <header class="tichet__check">

                <h2 class="ticket__check-title">
                    Проверка билета
                </h2>

            </header>

            <div class="ticket__info-wrapper">

                <div
                    @class([
                        'ticket-verification',
                        'ticket-verification_valid' =>
                            $verificationStatus === 'valid',
                        'ticket-verification_invalid' =>
                            $verificationStatus === 'invalid',
                        'ticket-verification_expired' =>
                            $verificationStatus === 'expired',
                    ])
                >
                    {{ $verificationMessage }}
                </div>

                <p class="ticket__info">
                    Фильм:

                    <span class="ticket__details">
                        {{ $booking->showtime->movie->title }}
                    </span>
                </p>

                <p class="ticket__info">
                    Зал:

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
                    Код бронирования:

                    <span class="ticket__details ticket__code">
                        {{ $booking->code }}
                    </span>
                </p>

            </div>

        </section>

    </main>

@endsection