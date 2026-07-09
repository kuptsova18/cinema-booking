@extends('layouts.admin')

@section('title', 'Авторизация | ИдёмВКино')

@section('content')
    <main>
        <section class="login">
            <header class="login__header">
                <h2 class="login__title">
                    Авторизация
                </h2>
            </header>

            <div class="login__wrapper">
                @if (session('success'))
                    <p class="conf-step__wrapper__save-status">
                        {{ session('success') }}
                    </p>
                @endif

                @if ($errors->any())
                    <div class="explanation-text">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form class="login__form" action="{{ route('admin.login.store') }}" method="POST">
                    @csrf

                    <label class="login__label">
                        E-mail
                        <input class="login__input" type="email" name="email" placeholder="example@domain.ru"
                            value="{{ old('email') }}" required autofocus autocomplete="email">
                    </label>

                    <label class="login__label">

                        Пароль

                        <input class="login__input" type="password" name="password" required
                            autocomplete="current-password">

                    </label>

                    <div class="text-center">

                        <input type="submit" value="Авторизоваться" class="login__button">

                    </div>
                </form>
            </div>
        </section>
    </main>
@endsection