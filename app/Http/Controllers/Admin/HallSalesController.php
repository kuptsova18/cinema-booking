<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hall;
use Illuminate\Http\RedirectResponse;

class HallSalesController extends Controller
{
    public function update(Hall $hall): RedirectResponse
    {
        if ($hall->is_active) {
            $hall->update(['is_active'=>false,]);
            return redirect()
                ->to(route('admin.dashboard') . '#hall-sales')
                ->with('sales_success', "Продажи для зала «{$hall->name}» закрыты.");
        }

        if (!$hall->rows_count || !$hall->seats_per_row || $hall->seats()->count() === 0) {
            return redirect()
                ->to(route('admin.dashboard') . '#hall-sales')
                ->with('sales_error', "Сначала настройте места в зале «{$hall->name}»");
        }

        if ($hall->standard_price === null || $hall->vip_price === null) {
            return redirect()
                ->to(route('admin.dashboard') . '#hall-sales')
                ->with(
                    'sales_error',
                    "Сначала установите цены для зала «{$hall->name}»."
                );
        }
        if ($hall->showtimes()->count() === 0) {
            return redirect()
                ->to(route('admin.dashboard') . '#hall-sales')
                ->with(
                    'sales_error',
                    "Для зала «{$hall->name}» нет ни одного сеанса."
                );
        }
        $hall->update([
            'is_active' => true,
        ]);

        return redirect()
            ->to(route('admin.dashboard') . '#hall-sales')
            ->with(
                'sales_success',
                "Продажи для зала «{$hall->name}» открыты."
            );

    }
}
