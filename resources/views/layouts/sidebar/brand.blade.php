<a class="fw-semibold text-dual d-flex align-items-center" href="{{ route('dashboard') }}">
    <span class="smini-visible">
        {{-- <img class="img-fluid" width="100" src="{{ asset('favicon.ico') }}" alt="Logo"> --}}
        AP
    </span>
    <span class="smini-hide fs-5 tracking-wider">
        {{-- <img class="img-fluid" src="{{ asset('logo.png') }}" alt="Logo"> --}}
        {{ config('app.name', 'Demo APP') }}
    </span>
</a>
<a class="d-lg-none btn btn-sm btn-alt-secondary ms-1" data-toggle="layout" data-action="sidebar_close" href="javascript:void(0)">
    <i class="fa fa-fw fa-times"></i>
</a>
