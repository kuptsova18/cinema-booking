document.addEventListener('click', (event) => {
    const openButton = event.target.closest(
        '[data-popup-open]'
    );

    if (openButton) {
        event.preventDefault();

        const popupId = openButton.dataset.popupOpen;
        const popup = document.getElementById(popupId);

        if (!popup) {
            console.error(
                `Модальное окно с id="${popupId}" не найдено.`
            );

            return;
        }

        popup.classList.add('active');
        document.body.classList.add('popup-is-open');

        return;
    }

    const closeButton = event.target.closest(
        '[data-popup-close]'
    );

    if (closeButton) {
        event.preventDefault();

        const popup = closeButton.closest('.popup');

        if (popup) {
            popup.classList.remove('active');
        }

        document.body.classList.remove('popup-is-open');

        return;
    }

    /*
     * Закрытие при нажатии на затемнённый фон.
     */
    if (event.target.classList.contains('popup')) {
        event.target.classList.remove('active');
        document.body.classList.remove('popup-is-open');
    }
});

/**
 * Закрытие по клавише Escape.
 */
document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') {
        return;
    }

    const activePopup = document.querySelector(
        '.popup.active'
    );

    if (activePopup) {
        activePopup.classList.remove('active');
        document.body.classList.remove('popup-is-open');
    }
});

document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector(
        '[data-hall-configuration-form]'
    );

    if (!form) {
        return;
    }

    const seatTypesInput = form.querySelector(
        '[data-seat-types-input]'
    );

    if (!seatTypesInput) {
        return;
    }

    const chairClasses = {
        standard: 'conf-step__chair_standart',
        vip: 'conf-step__chair_vip',
        disabled: 'conf-step__chair_disabled',
    };

    function getNextSeatType(currentType) {
        if (currentType === 'standard') {
            return 'vip';
        }

        if (currentType === 'vip') {
            return 'disabled';
        }

        return 'standard';
    }

    function synchronizeSeatTypes() {
        const seatTypes = {};

        form.querySelectorAll('[data-seat-key]').forEach(
            (chair) => {
                seatTypes[chair.dataset.seatKey] =
                    chair.dataset.seatType;
            }
        );

        seatTypesInput.value = JSON.stringify(seatTypes);
    }

    /*
     * Используем один общий обработчик для всех кресел.
     */
    form.addEventListener('click', (event) => {
        const chair = event.target.closest(
            '[data-seat-key]'
        );

        if (!chair || !form.contains(chair)) {
            return;
        }

        const currentType =
            chair.dataset.seatType || 'standard';

        const nextType =
            getNextSeatType(currentType);

        chair.classList.remove(
            chairClasses.standard,
            chairClasses.vip,
            chairClasses.disabled
        );

        chair.classList.add(
            chairClasses[nextType]
        );

        chair.dataset.seatType = nextType;

        synchronizeSeatTypes();
    });

    /*
     * Непосредственно перед отправкой формы
     * ещё раз записываем типы всех кресел.
     */
    form.addEventListener('submit', () => {
        synchronizeSeatTypes();
    });

    synchronizeSeatTypes();
});

