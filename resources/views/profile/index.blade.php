<x-app-layout title="{{ $title }}">
    <x-slot name="styles">
        <script src="{{ asset('assets/js/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    </x-slot>

    <x-header title='{{ $title }}'>
        <li class="breadcrumb-item"><a class="link-fx" href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item">{{ $title }}</li>
    </x-header>


    <div class="row mt-5">
        <div class="col-sm-8 col-lg-8 mx-auto">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">My Profile</h3>
                    <div class="block-options">
                        <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-alt-primary">
                            <i class="fa fa-fw fa-pencil-alt me-1"></i> Edit Profile
                        </a>
                    </div>
                </div>

                <div class="block-content">
                    <div class="row push">
                        <div class="col-lg-4 d-flex justify-content-center align-items-center mb-4 mb-lg-0">
                            <!-- Avatar -->
                            <div class="text-center">
                                <img style="border: 2px solid #6571ff" class="img-avatar img-avatar180 img-avatar-thumb bg-body" src="{{ auth()->user()->image ?? asset('avatar.png') }}" alt="Profile">
                            </div>
                        </div>
                        <div class="col-lg-8 d-flex align-items-center">
                            <!-- Profile details -->
                            <div class="w-100">
                                <div class="d-flex border-bottom pb-3 mb-3">
                                    <span class="text-muted fw-medium w-25">Name</span>
                                    <span class="fw-semibold text-dark">{{ auth()->user()->name }}</span>
                                </div>
                                <div class="d-flex border-bottom pb-3 mb-3">
                                    <span class="text-muted fw-medium w-25">Email</span>
                                    <span class="fw-semibold text-dark">{{ auth()->user()->email }}</span>
                                </div>
                                <!-- Example fields -->
                                <div class="d-flex border-bottom pb-3 mb-3">
                                    <span class="text-muted fw-medium w-25">Mobile</span>
                                    <span class="fw-semibold text-dark">{{ auth()->user()->mobile ?? 'N/A' }}</span>
                                </div>
                                <div class="d-flex border-bottom pb-3 mb-3">
                                    <span class="text-muted fw-medium w-25">Branch</span>
                                    <span class="fw-semibold text-dark">{{ auth()->user()->branch->name ?? 'N/A' }}</span>
                                </div>
                                <div class="d-flex border-bottom pb-3 mb-3">
                                    <span class="text-muted fw-medium w-25">Joined</span>
                                    <span class="fw-semibold text-dark">{{ auth()->user()->created_at?->format('F d, Y') }}</span>
                                </div>
                                <!-- Any other metadata if any -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-slot name="scripts">
        <script src="{{ asset('assets/js/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
        @foreach(['success', 'error'] as $msg)
        @if(session()->has($msg))
        <script>
            showToast('{{ $msg }}', '{{ session()->get($msg) }}')
        </script>
        @endif
        @endforeach
    </x-slot>
</x-app-layout>
