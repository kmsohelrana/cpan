<x-app-layout title="{{ $title }}">
    <x-slot name="styles">
        @include('roles.style')
    </x-slot>
    <x-header title='{{ $title }}'>
        <li class="breadcrumb-item"><a class="link-fx" href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a class="link-fx" href="{{ route('roles.index') }}">Roles</a></li>
        <li class="breadcrumb-item">Create</li>
    </x-header>
    <div class="block block-rounded h-100">
        <div class="block-content">
            <form action="{{ route('roles.store') }}" method="POST">
                @csrf
                @include('roles.form')
            </form>
        </div>
    </div>

    <x-slot name="scripts">
        @include('scripts.select-2')
    </x-slot>
</x-app-layout>
