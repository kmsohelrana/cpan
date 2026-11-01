<x-app-layout title="{{ $title }}">
    <x-slot name="styles">
        @include('styles.flatpickr')
        <link rel="stylesheet" href="{{ asset('assets/js/plugins/sweetalert2/sweetalert2.min.css') }}">
        @include('styles.select-2')
    </x-slot>

    <x-header title='{{ $title }}'>
        <li class="breadcrumb-item"><a class="link-fx" href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item">{{ $title }}</li>
    </x-header>

    <div class="row">
        <div class="col-12">
            @include('notifications.filter')
        </div>
        <div class="col-12">
            <div class="data-table">
                {!! $data !!}
            </div>
        </div>
    </div>

    <x-slot name="scripts">
        @include('notifications.script')
    </x-slot>

</x-app-layout>
