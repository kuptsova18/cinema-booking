<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Показ страницы авторизации.
     */
    public function create(): View
    {
        return view('admin.login');
    }

    /**
     * Обработка формы авторизации.
     */

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate(
            [
                'email' => [
                    'required',
                    'email',
                    'max:255',
                ],
                'password' => [
                    'required',
                    'string',
                ],
            ],
            [
                'email.required' => 'Введите email.',
                'email.email' => 'Введите корректный email.',
                'password.required' => 'Введите пароль.',
            ]
        );

        if (!Auth::attempt($credentials)) {
            return back()->withErrors(['email' => 'Неверный email или пароль.',])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Вы успешно вышли из системы.');
    }

}
