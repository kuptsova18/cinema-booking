<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MovieController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'title' => trim((string) $request->input('title')),
            'description' => trim((string) $request->input('description')),
            'country' => trim((string) $request->input('country')),
        ]);

        $validated = $request->validateWithBag(
            'movieCreation',
            [
                'title' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'duration_minutes' => [
                    'required',
                    'integer',
                    'min:1',
                    'max:1000',
                ],

                'description' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'country' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'poster' => [
                    'nullable',
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'max:5120',
                ],
            ],
            [
                'title.required' =>
                    'Введите название фильма.',

                'title.max' =>
                    'Название фильма не должно превышать 255 символов.',

                'duration_minutes.required' =>
                    'Укажите продолжительность фильма.',

                'duration_minutes.integer' =>
                    'Продолжительность должна быть целым числом.',

                'duration_minutes.min' =>
                    'Продолжительность должна быть больше нуля.',

                'duration_minutes.max' =>
                    'Указана слишком большая продолжительность фильма.',

                'description.required' =>
                    'Введите описание фильма.',

                'description.max' =>
                    'Описание не должно превышать 255 символов.',

                'country.required' =>
                    'Укажите страну производства.',

                'country.max' =>
                    'Название страны не должно превышать 255 символов.',

                'poster.image' =>
                    'Постер должен быть изображением.',

                'poster.mimes' =>
                    'Допустимы изображения JPG, JPEG, PNG и WEBP.',

                'poster.max' =>
                    'Размер постера не должен превышать 5 МБ.',
            ]
        );

        $posterPath = null;

        if ($request->hasFile('poster')) {
            $posterPath = $request->file('poster')->store('posters', 'public');
        }

        Movie::create([
            'title' => $validated['title'],
            'duration_minutes' =>
                $validated['duration_minutes'],
            'description' => $validated['description'],
            'country' => $validated['country'],
            'poster_path' => $posterPath,
        ]);

        return redirect()->route('admin.dashboard')->with(
            'movie_success',
            'Фильм успешно добавлен.'
        );
    }

    public function destroy(Movie $movie): RedirectResponse
    {
        /*
         * Фильм нельзя удалить,
         * если для него уже созданы сеансы.
         */
        if ($movie->showtimes()->exists()) {
            return redirect()
                ->route('admin.dashboard')
                ->with(
                    'movie_error',
                    'Нельзя удалить фильм, для которого созданы сеансы.'
                );
        }

        /*
         * Удаляем файл постера.
         */
        if ($movie->poster_path) {
            Storage::disk('public')
                ->delete($movie->poster_path);
        }

        $movieTitle = $movie->title;

        $movie->delete();

        return redirect()
            ->route('admin.dashboard')
            ->with(
                'movie_success',
                "Фильм «{$movieTitle}» успешно удалён."
            );
    }
}
