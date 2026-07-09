<div class="popup" id="add-hall-popup">
    <div class="popup__container">
        <div class="popup__header">
            <h2 class="popup__title">
                Добавление зала
                <button type="button" class=" popup__dismiss" data-popup-close>
                    <img src="{{ asset('admin-assets/i/close.png') }}" alt="Закрыть"></button>
            </h2>
            <div class="popup__wrapper">
                <form action="{{ route('admin.halls.store') }}" method="post">
                    @csrf
                    <label class="conf-step__label conf-step__label-fullsize">
                        Название зала
                        <input class="conf-step__input" type="text" name="name" placeholder="Например: Зал 1" required>
                    </label>
                    @error('name', 'hallCreation')
                        <p class="explanation-text">
                            {{ $message }}
                        </p>
                    @enderror

                    <div class="conf-step__buttons text-center">
                        <input type="submit" value="Добавить зал" class="conf-step__button conf-step__button-accent">

                        <button type="button" class="conf-step__button conf-step__button-regular" data-popup-close>
                            Отменить
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>