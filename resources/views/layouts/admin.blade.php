<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>@yield('title', 'ИдёмВКино')</title>

    {{-- Стили административной части --}}
    <link
    rel="stylesheet"
    href="{{ asset('admin-assets/css/normalize.css') }}"
>

<link
    rel="stylesheet"
    href="{{ asset('admin-assets/css/styles.css') }}"
>
    <link
        href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900&subset=cyrillic,cyrillic-ext,latin-ext"
        rel="stylesheet">
</head>

<body>
    <header class="page-header">
        <h1 class="page-header__title"> Идём<span>в</span>кино </h1>
        <span class="page-header__subtitle"> Администраторская</span>
    </header>

    {{-- Здесь будет содержимое конкретной страницы --}}
    @yield('content')

    {{-- Основной JavaScript административной части --}}
    <script src="{{ asset('admin-assets/js/accordeon.js') }}"></script>

    {{-- Здесь страницы смогут подключать дополнительные скрипты --}}
    <script src="{{ asset('admin-assets/js/admin.js') }}" defer></script>
    @stack('scripts')
</body>

</html>