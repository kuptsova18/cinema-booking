<div class="popup" {{ $errors->movieCreation->any() ? 'active' : '' }} id="add-movie-popup">
    <div class="popup__container">
        <div class="popup__content">
            <div class="popup__header">
                <h2 class="popup__title">
                    Добавление фильма
                    <button type="button" class="popup__dismiss" data-popup-close aria-label="Закрыть окно">
                        <img src="{{ asset('admin-assets/i/close.png') }}" alt="">
                    </button>
                </h2>
            </div>
            <div class="popup__wrapper">
                <form action="{{ route('admin.movies.store') }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <label class="conf-step__label conf-step__label-fullsize">
                        Название фильма
                        <input class="conf-step__input" type="text" value="{{ old('title') }}" placeholder="Например, &laquo;Гражданин Кейн&raquo;"
                            name="title" required>
                    </label>
                     @error('title', 'movieCreation')
                        <p class="explanation-text">
                            {{ $message }}
                        </p>
                    @enderror
                    <label class="conf-step__label conf-step__label-fullsize">
                        Продолжительность фильма (мин.)
                        <input type="number" class="conf-step__input" name="duration_minutes" value="{{ old('duration_minutes') }}" min="1" max="1000" required
                        >
                    </label>
                    @error('duration_minutes', 'movieCreation')
                        <p class="explanation-text">
                            {{ $message }}
                        </p>
                    @enderror
                    
                    <label class="conf-step__label conf-step__label-fullsize">
                        Страна
                        <input class="conf-step__input" type="text" name="country" value="{{ old('country') }}" placeholder="Россия" required>
                    </label>
                    @error('country', 'movieCreation')
                        <p class="explanation-text">
                            {{ $message }}
                        </p>
                    @enderror

                    <label class="conf-step__label conf-step__label-fullsize" for="name">
                        Описание фильма
                        <textarea class="conf-step__input" type="text" name="description" value="{{ old('description') }}" maxlength="255" required></textarea>
                    </label>
                     @error('description', 'movieCreation')
                        <p class="explanation-text">
                            {{ $message }}
                        </p>
                    @enderror

                    <label class="conf-step__label conf-step__label-fullsize">
                        Постер

                        <input
                            type="file" class="conf-step__input" name="poster" accept=".jpg,.jpeg,.png,.webp">
                    </label>

                    @error('poster', 'movieCreation')
                        <p class="explanation-text">
                            {{ $message }}
                        </p>
                    @enderror
                    <div class="conf-step__buttons text-center">
                        <button type="submit" value="Добавить фильм" class="conf-step__button conf-step__button-accent">Добавить фильм</button>
                        <button type="button" class="conf-step__button conf-step__button-regular" data-popup-close >Отменить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>