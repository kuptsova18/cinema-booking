<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\HallController;
use App\Http\Controllers\Admin\HallSalesController;
use App\Http\Controllers\Admin\MovieController;
use App\Http\Controllers\Admin\ShowTimeController;
use App\Http\Controllers\Client\BookingController;
use App\Http\Controllers\Client\ScheduleController;
use App\Http\Controllers\Client\SeatSelectionController;
use Illuminate\Support\Facades\Route;


Route::get('/admin/login', [AuthController::class, 'create'])
    ->name('login');
Route::post('/admin/login', [AuthController::class, 'store'])->middleware('throttle:5,1')
    ->name('admin.login.store');

Route::prefix('admin')
    ->name('admin.')
    ->middleware('auth')
    ->group(function (): void {

        /*
         * Главная страница администратора:
         * http://127.0.0.1:8000/admin
         */
        Route::get('/', DashboardController::class)
            ->name('dashboard');

        /*
         * Выход из административной части.
         */
        Route::post('/logout', [AuthController::class, 'destroy'])
            ->name('logout');

        Route::post('/halls', [HallController::class, 'store'])
            ->name('halls.store');

        Route::delete('/halls/{hall}', [HallController::class, 'destroy'])
            ->name('halls.destroy');

        Route::put('/halls/{hall}/configuration', [HallController::class, 'updateConfiguration'])
            ->name('halls.configuration.update');

        Route::put('/halls/{hall}/prices', [HallController::class, 'updatePrices'])
            ->name('halls.prices.update');

        Route::post('/movies', [MovieController::class, 'store'])->name('movies.store');

        Route::delete('/movies/{movie}', [MovieController::class, 'destroy'])->name('movies.destroy');

        Route::post('/showtimes', [ShowTimeController::class, 'store'])->name('showtimes.store');

        Route::delete('/showtimes/{showtime}', [ShowTimeController::class, 'destroy'])->name('showtimes.destroy');

        Route::put('/halls/{hall}/sales', [HallSalesController::class, 'update'])->name('halls.sales.update');

    });

Route::get('/', [ScheduleController::class, 'index'])->name('client.schedule');

Route::get('/showtimes/{showtime}/seats', [SeatSelectionController::class, 'show'])->name('client.showtimes.seats');

Route::post('/showtimes/{showtime}/bookings', [BookingController::class, 'store'])->name('client.bookings.store');

Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('client.bookings.show');

Route::get('/bookings/{booking}/verify', [BookingController::class, 'verify'])->name('client.bookings.verify');






