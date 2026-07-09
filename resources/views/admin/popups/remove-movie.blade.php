<div class="popup" id="remove-movie-popup-{{ $movie->id }}">
    <div class="popup__container">
        <div class="popup__content">
            <div class="popup__header">
                <h2 class="popup__title">
                    Удаление фильма
                    <button type="button" class="popup__dismiss" data-popup-close aria-label="Закрыть окно">
                        <img src="{{ asset('admin-assets/i/close.png') }}" alt="">
                    </button>
                </h2>
            </div>
            <div class="popup__wrapper">
                <form action="{{ route('admin.movies.destroy', $movie) }}" method="post">
                    @csrf
                    @method('DELETE')
                    <p class="conf-step__paragraph">
                        Вы действительно хотите удалить фильм
                        <strong>«{{ $movie->title }}»</strong>?
                    </p>
                    <!-- В span будет подставляться название фильма -->
                    <div class="conf-step__buttons text-center">
                        <button type="submit" class="conf-step__button
                                   conf-step__button-accent">
                            Удалить
                        </button>

                        <button type="button" class="conf-step__button
                                   conf-step__button-regular" data-popup-close>
                            Отменить
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>