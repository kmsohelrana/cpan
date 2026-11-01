@props(['title'])

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <meta name="_token" content="{{ csrf_token() }}">
    <title>{{ $title . ' - ' . config('app.name', 'Acquiring Platform') }}</title>
    <link rel="stylesheet" id="css-main" href="{{ asset('assets/css/oneui.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/sidebar-search.css') }}">
    {{ $styles ?? '' }}
</head>

<body>
    @include('layouts.loader')

    @guest
    @include('layouts.auth')
    @else
    @include('layouts.master')
    @endguest

    <script src="{{ asset('assets/js/oneui.app.min.js') }}"></script>
    <script src="{{ asset('assets/js/lib/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/sidebar-search.js') }}"></script>
    {{ $scripts ?? '' }}
    @include('scripts.firebase')
    @include('scripts.notifications')


</body>

</html>
