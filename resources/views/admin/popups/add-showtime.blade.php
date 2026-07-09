<div class="popup {{ $errors->showtimeCreation->any() ? 'active' : '' }}" id="add-showtime-popup">
    <div class="popup__container">
        <div class="popup__content">
            <div class="popup__header">
                <h2 class="popup__title">
                    Добавление сеанса
                    <button type="button" class="popup__dismiss" data-popup-close aria-label="Закрыть окно">
                        <img=src="{{ asset('admin-assets/i/close.png') }}" alt="">
                    </button>
                </h2>
            </div>
            <div class="popup__wrapper">
                <form action="{{ route('admin.showtimes.store') }}" method="post">
                    @csrf

                    <label class="conf-step__label conf-step__label-fullsize">
                        Название зала
                        <select class="conf-step__input" name="hall_id" required>
                            <option value="">
                                Выберите зал
                            </option>
                            @foreach ($halls as $hall)

                                <option value="{{ $hall->id }}" @selected(
                                    (int) old('hall_id')
                                    === $hall->id
                                )>
                                    {{ $hall->name }}
                                </option>

                            @endforeach
                        </select>
                    </label>
                    @error('hall_id', 'showtimeCreation')
                        <p class="explanation-text">
                            {{ $message }}
                        </p>
                    @enderror
                    <label class="conf-step__label conf-step__label-fullsize">
                        Название фильма
                        <select class="conf-step__input" name="movie_id" required>
                            <option value="">
                                Выберите фильм
                            </option>

                            @foreach ($movies as $movie)

                                <option value="{{ $movie->id }}" @selected(
                                    (int) old('movie_id')
                                    === $movie->id
                                )>
                                    {{ $movie->title }}
                                    —
                                    {{ $movie->duration_minutes }}
                                    мин.
                                </option>

                            @endforeach
                        </select>
                    </label>
                    @error('movie_id', 'showtimeCreation')
                        <p class="explanation-text">
                            {{ $message }}
                        </p>
                    @enderror

                    <label class="conf-step__label conf-step__label-fullsize">
                        Дата и время начала
                        <input class="conf-step__input" type="datetime-local" value="{{ old('starts_at') }}"
                            name="starts_at" required>
                    </label>
                    @error('starts_at', 'showtimeCreation')
                        <p class="explanation-text">
                            {{ $message }}
                        </p>
                    @enderror

                    <div class="conf-step__buttons text-center">
                        <button class="conf-step__button conf-step__button-accent" type="submit">Добавить сеанс</button>
                        <button class="conf-step__button conf-step__button-regular" type="button" data-popup-close>Отменить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>