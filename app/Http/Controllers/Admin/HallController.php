<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hall;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Seat;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class HallController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->merge(['name' => trim((string) $request->input('name'))]);

        $validated = $request->validateWithBag(
            'hallCreation',
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    'unique:halls,name',
                ],
            ],
            [
                'name.required' => 'Введите название зала.',
                'name.string' => 'Название зала должно быть строкой.',
                'name.max' => 'Название зала не должно превышать 255 символов.',
                'name.unique' => 'Зал с таким названием уже существует.',
            ]
        );

        Hall::create(['name' => $validated['name']]);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Зал успешно создан.');
    }

    public function destroy(Hall $hall): RedirectResponse
    {
        if ($hall->showtimes()->exists()) {
            return redirect()->route('admin.dashboard')->with('error', 'Нельзя удалить зал, для которого созданы сеансы.');
        }

        $hasTickets = $hall->seats()->whereHas('tickets')->exists();

        if ($hasTickets) {
            return redirect()->route('admin.dashboard')->with('error', 'Нельзя удалить зал, для которого уже оформлены билеты.');
        }

        $hallName = $hall->name;

        $hall->delete();

        return redirect()->route('admin.dashboard')->with('success', "Зал «{$hallName}» успешно удалён.");
    }

    public function updateConfiguration(
        Request $request,
        Hall $hall
    ): RedirectResponse {
        $validated = $request->validateWithBag(
            'hallConfiguration',
            [
                'configuration_hall_id' => [
                    'required',
                    'integer',
                ],

                'rows_count' => [
                    'required',
                    'integer',
                    'min:1',
                    'max:20',
                ],

                'seats_per_row' => [
                    'required',
                    'integer',
                    'min:1',
                    'max:30',
                ],

                'seat_types' => [
                    'nullable',
                    'json',
                ],
            ],
            [
                'rows_count.required' =>
                    'Укажите количество рядов.',

                'rows_count.integer' =>
                    'Количество рядов должно быть целым числом.',

                'rows_count.min' =>
                    'В зале должен быть хотя бы один ряд.',

                'rows_count.max' =>
                    'Количество рядов не должно превышать 20.',

                'seats_per_row.required' =>
                    'Укажите количество мест в ряду.',

                'seats_per_row.integer' =>
                    'Количество мест должно быть целым числом.',

                'seats_per_row.min' =>
                    'В ряду должно быть хотя бы одно место.',

                'seats_per_row.max' =>
                    'Количество мест в ряду не должно превышать 30.',

                'seat_types.json' =>
                    'Не удалось определить типы кресел.',
            ]
        );

        /*
         * Дополнительная проверка:
         * форма должна относиться к залу из маршрута.
         */
        if (
            (int) $validated['configuration_hall_id']
            !== (int) $hall->id
        ) {
            abort(422, 'Выбран неверный зал.');
        }

        $rowsCount =
            (int) $validated['rows_count'];

        $seatsPerRow =
            (int) $validated['seats_per_row'];

        $seatTypes = json_decode(
            $validated['seat_types'] ?? '{}',
            true
        );

        if (!is_array($seatTypes)) {
            $seatTypes = [];
        }

        $allowedTypes = [
            'standard',
            'vip',
            'disabled',
        ];

        /*
         * Получаем текущие места до изменения схемы.
         */
        $existingSeats = $hall->seats()
            ->orderBy('row_number')
            ->orderBy('seat_number')
            ->get();

        $existingTypes = $existingSeats->mapWithKeys(
            function (Seat $seat): array {
                return [
                    $seat->row_number
                    . '-'
                    . $seat->seat_number
                    => $seat->type,
                ];
            }
        )->all();

        /*
         * Определяем, действительно ли изменились размеры.
         */
        $dimensionsChanged =
            (int) $hall->rows_count !== $rowsCount
            ||
            (int) $hall->seats_per_row !== $seatsPerRow
            ||
            $existingSeats->count()
            !== $rowsCount * $seatsPerRow;

        $hasTickets = $hall->seats()
            ->whereHas('tickets')
            ->exists();

        if ($hasTickets) {
            return redirect()
                ->to(
                    route('admin.dashboard', [
                        'hall' => $hall->id,
                    ]) . '#hall-configuration'
                )
                ->with(
                    'error',
                    'Нельзя изменить схему зала, потому что на его места уже оформлены билеты.'
                );
        }

        DB::transaction(
            function () use ($hall, $rowsCount, $seatsPerRow, $seatTypes, $existingTypes, $allowedTypes, $dimensionsChanged): void {
                /*
                 * Сохраняем размеры именно выбранного зала.
                 */
                $hall->update([
                    'rows_count' => $rowsCount,
                    'seats_per_row' => $seatsPerRow,
                ]);

                /*
                 * Если размеры изменились,
                 * только тогда перестраиваем схему.
                 */
                if ($dimensionsChanged) {
                    $hall->seats()->delete();

                    $newSeats = [];
                    $now = now();

                    for (
                        $rowNumber = 1;
                        $rowNumber <= $rowsCount;
                        $rowNumber++
                    ) {
                        for (
                            $seatNumber = 1;
                            $seatNumber <= $seatsPerRow;
                            $seatNumber++
                        ) {
                            $seatKey =
                                $rowNumber . '-' . $seatNumber;

                            /*
                             * Сначала берём тип из формы.
                             * Если его нет — сохраняем старый тип
                             * для совпадающего ряда и места.
                             */
                            $seatType =
                                $seatTypes[$seatKey]
                                ??
                                $existingTypes[$seatKey]
                                ??
                                'standard';

                            if (
                                !in_array(
                                    $seatType,
                                    $allowedTypes,
                                    true
                                )
                            ) {
                                $seatType = 'standard';
                            }

                            $newSeats[] = [
                                'hall_id' => $hall->id,
                                'row_number' => $rowNumber,
                                'seat_number' => $seatNumber,
                                'type' => $seatType,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ];
                        }
                    }

                    Seat::insert($newSeats);

                    return;
                }

                /*
                 * Если размеры не менялись,
                 * кресла не удаляем.
                 * Обновляем только их типы.
                 */
                foreach ($seatTypes as $seatKey => $seatType) {
                    if (
                        !in_array(
                            $seatType,
                            $allowedTypes,
                            true
                        )
                    ) {
                        continue;
                    }

                    if (
                        !preg_match(
                            '/^(\d+)-(\d+)$/',
                            (string) $seatKey,
                            $matches
                        )
                    ) {
                        continue;
                    }

                    $rowNumber = (int) $matches[1];
                    $seatNumber = (int) $matches[2];

                    if (
                        $rowNumber < 1
                        || $rowNumber > $rowsCount
                        || $seatNumber < 1
                        || $seatNumber > $seatsPerRow
                    ) {
                        continue;
                    }

                    $hall->seats()
                        ->where(
                            'row_number',
                            $rowNumber
                        )
                        ->where(
                            'seat_number',
                            $seatNumber
                        )
                        ->update([
                            'type' => $seatType,
                        ]);
                }
            }
        );

        return redirect()
            ->to(
                route('admin.dashboard', [
                    'hall' => $hall->id,
                ]) . '#hall-configuration'
            )
            ->with(
                'success',
                "Конфигурация зала «{$hall->name}» сохранена."
            );
    }

    public function updatePrices(Request $request, Hall $hall): RedirectResponse
    {
        $validated = $request->validateWithBag(
            'hallPrices',
            [
                'price_hall_id' => [
                    'required',
                    'integer',
                ],

                'standard_price' => [
                    'required',
                    'numeric',
                    'min:0',
                    'max:1000000',
                ],

                'vip_price' => [
                    'required',
                    'numeric',
                    'min:0',
                    'max:1000000',
                ],
            ],
            [
                'standard_price.required' =>
                    'Укажите стоимость обычного кресла.',

                'standard_price.numeric' =>
                    'Стоимость обычного кресла должна быть числом.',

                'standard_price.min' =>
                    'Стоимость обычного кресла не может быть отрицательной.',

                'standard_price.max' =>
                    'Указана слишком большая стоимость обычного кресла.',

                'vip_price.required' =>
                    'Укажите стоимость VIP-кресла.',

                'vip_price.numeric' =>
                    'Стоимость VIP-кресла должна быть числом.',

                'vip_price.min' =>
                    'Стоимость VIP-кресла не может быть отрицательной.',

                'vip_price.max' =>
                    'Указана слишком большая стоимость VIP-кресла.',
            ]
        );

        if ((int) $validated['price_hall_id'] !== (int) $hall->id) {
            abort(422, 'Выбран неверный зал!');
        }

        $hall->update([
            'standard_price' => round((float) $validated['standard_price'], 2),
            'vip_price' => round((float) $validated['vip_price'], 2),
        ]);

        return redirect()->to(route('admin.dashboard', ['hall' => $hall->id,]) . '#hall-prices')
            ->with('price_success', "Цены для зала «{$hall->name}» сохранены.");
    }
}
