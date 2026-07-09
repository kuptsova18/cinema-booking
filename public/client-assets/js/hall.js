document.addEventListener('DOMContentLoaded', () => {
    const seats = document.querySelectorAll(
        '.buying-scheme__chair[data-seat-id]:not(:disabled)'
    );

    const selectedSeatIdsInput = document.querySelector(
        '[data-selected-seat-ids]'
    );

    const bookingButton = document.querySelector(
        '[data-booking-button]'
    );

    if (!selectedSeatIdsInput || !bookingButton) {
        return;
    }

    /*
     * Восстанавливаем выбранные места после ошибки валидации.
     * Например, hidden input может содержать: "1,2,5".
     */
    const selectedSeatIds = new Set(
        selectedSeatIdsInput.value
            .split(',')
            .map((seatId) => seatId.trim())
            .filter((seatId) => seatId !== '')
    );

    function updateBookingButton() {
        selectedSeatIdsInput.value = Array
            .from(selectedSeatIds)
            .join(',');

        bookingButton.disabled =
            selectedSeatIds.size === 0;
    }

    seats.forEach((seat) => {
        const seatId = seat.dataset.seatId;

        /*
         * Восстанавливаем визуальное выделение
         * после возврата с ошибкой.
         */
        if (selectedSeatIds.has(seatId)) {
            seat.classList.add(
                'buying-scheme__chair_selected'
            );
        }

        seat.addEventListener('click', () => {
            if (selectedSeatIds.has(seatId)) {
                selectedSeatIds.delete(seatId);

                seat.classList.remove(
                    'buying-scheme__chair_selected'
                );
            } else {
                selectedSeatIds.add(seatId);

                seat.classList.add(
                    'buying-scheme__chair_selected'
                );
            }

            updateBookingButton();
        });
    });

    updateBookingButton();
});