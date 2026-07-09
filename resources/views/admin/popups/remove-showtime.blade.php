<div class="popup" id="remove-showtime-popup-{{ $showtime->id }}">
  <div class=" popup__container">
    <div class="popup__content">
        <div class="popup__header">
            <h2 class="popup__title">
                Снятие с сеанса
                <button type="button" class="popup__dismiss" data-popup-close aria-label="Закрыть окно">
                    <img src="{{ asset('admin-assets/i/close.png') }}" alt="">
                </button>
            </h2>
        </div>
        <div class="popup__wrapper">
            <form action="{{ route('admin.showtimes.destroy', ['showtime' => $showtime->id,]) }}" method="post">

                @csrf
                @method('DELETE')
                <p class="conf-step__paragraph">Удалить сеанс фильма
                    <strong>
                        «{{ $showtime->movie->title }}»
                    </strong>?
                </p>
                <p class="conf-step__paragraph">
                    Зал:
                    <strong>
                        {{ $showtime->hall->name }}
                    </strong>
                </p>

                <p class="conf-step__paragraph">
                    Начало:
                    <strong>
                        {{ $showtime->starts_at->format('d.m.Y H:i') }}
                    </strong>
                </p>

                <div class="conf-step__buttons text-center">
                    <button class="conf-step__button conf-step__button-accent" type="submit">Удалить сеанс</button>
                    <button class="conf-step__button conf-step__button-regular" type="button"
                        data-popup-close>Отменить</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>