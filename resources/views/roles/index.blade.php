<x-app-layout title="{{ $title }}">
    <x-slot name="styles">
        <link rel="stylesheet" href="{{ asset('assets/js/plugins/sweetalert2/sweetalert2.min.css') }}">
    </x-slot>


    <x-header title='{{ $title }}'>
        <li class="breadcrumb-item"><a class="link-fx" href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item">{{ $title }}</li>
    </x-header>

    <div class="row items-push">
        <div class="col">
            <div class="data-table">
                {!! $data !!}
            </div>
        </div>
    </div>

    @include('roles.modals.show')

    <x-slot name="scripts">
        @include('roles.script')
    </x-slot>
</x-app-layout>
