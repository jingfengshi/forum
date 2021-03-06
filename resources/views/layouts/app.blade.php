<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->




    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        .level{display: flex;align-items: center}
        .flex{
            flex: 1;
        }
        .ml-a{
            margin-left: auto;
        }
    </style>
    <script>
        window.App ={!! json_encode([
            'csrfToken'=>csrf_token(),
            'signedIn'=>Auth::check(),
            'user'=>Auth::user()
        ]) !!}
    </script>

    @yield('header')
</head>
<body>
    <div id="app">

        @include('layouts.nav')
        <main class="py-4">
            @yield('content')
        </main>

        <flash message="{{session('flash')}}"></flash>

    </div>


    @yield('scripts')
</body>
</html>
