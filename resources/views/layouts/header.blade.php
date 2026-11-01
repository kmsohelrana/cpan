<header id="page-header">
    <div class="content-header">
        <div class="d-flex align-items-center">
            <button type="button" class="btn btn-sm btn-alt-secondary me-2 d-lg-none" data-toggle="layout" data-action="sidebar_toggle">
                <i class="fa fa-fw fa-bars"></i>
            </button>
            <a style="color: #7987a1" class="sidebar-toggle d-none d-lg-block" href="javascript:void(0);" data-toggle="layout" data-action="sidebar_mini_toggle">
                <i class="fa fa-fw fa-bars"></i>
            </a>
        </div>
        <div id="sidebar-menu-search-wrapper" class="position-relative flex-grow-1 mx-2 ms-lg-4" style="max-width: 230px; min-width: 90px;">
            <input type="text" class="form-control" id="sidebar-menu-search" placeholder="Search menu..." autocomplete="off">
        </div>
        <button type="button" class="btn btn-sm btn-alt-secondary toggleTheme ms-auto" data-toggle="layout" data-action="dark_mode_toggle">
            <i class="far fa-moon"></i>
        </button>
        <div class="d-flex align-items-center">
            <div class="dropdown d-inline-block ms-2">
                <button type="button" class="btn btn-sm btn-alt-secondary position-relative" id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-fw fa-bell"></i>
                    @if(($unreadCount ?? 0) > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notification-badge">
                            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                        </span>
                    @else
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none" id="notification-badge"></span>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0 border-0 fs-sm" aria-labelledby="page-header-notifications-dropdown">
                    <div class="p-2 bg-body-light border-bottom rounded-top d-flex justify-content-between align-items-center">
                        <h5 class="dropdown-header mb-0">Notifications</h5>
                        @if(($unreadCount ?? 0) > 0)
                            <a href="javascript:void(0)" class="text-muted fs-xs me-2" id="mark-all-read">Mark all as read</a>
                        @else
                            <a href="javascript:void(0)" class="text-muted fs-xs me-2 d-none" id="mark-all-read">Mark all as read</a>
                        @endif
                    </div>
                    <ul class="nav-items mb-0" id="notification-list" style="max-height: 420px; overflow-y: auto;">
                        @forelse($headerNotifications ?? [] as $notification)
                            <li>
                                <a class="text-dark d-flex py-2 notification-item" href="javascript:void(0)" data-id="{{ $notification->id }}">
                                    <div class="flex-shrink-0 me-2 ms-3">
                                        <i class="fa fa-fw fa-bell text-primary"></i>
                                    </div>
                                    <div class="flex-grow-1 pe-2">
                                        <div class="fw-semibold">{{ $notification->title }}</div>
                                        <div class="text-muted">{{ Str::limit($notification->message, 70) }}</div>
                                        <span class="fw-medium text-muted">{{ $notification->created_at->diffForHumans() }}</span>
                                    </div>
                                </a>
                            </li>
                        @empty
                            <li class="px-3 py-3 text-muted text-center" id="no-notifications">No new notifications</li>
                        @endforelse
                    </ul>
                    <div class="p-2 border-top text-center">
                        <a class="d-inline-block fw-medium" href="{{ route('notifications.index') }}">
                            <i class="fa fa-fw fa-arrow-right me-1 opacity-50"></i> See All Notifications
                        </a>
                    </div>
                </div>
            </div>
            <div class="dropdown d-inline-block ms-2">
                <button type="button" class="btn btn-sm btn-alt-secondary d-flex align-items-center" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="rounded-circle" src="{{ authUser()->image ?? asset('avatar.png') }}" alt="Header Avatar" style="width: 21px;">
                    <span class="d-none d-sm-inline-block ms-2">{{ authUser()->name }}</span>
                    <i class="fa fa-fw fa-angle-down d-none d-sm-inline-block opacity-50 ms-1 mt-1"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-md dropdown-menu-end p-0 border-0" aria-labelledby="page-header-user-dropdown">
                    <div class="p-2">
                        <a class="dropdown-item d-flex align-items-center justify-content-between" href="{{ route('profile.index') }}">
                            <span class="fs-sm fw-medium">Profile</span>
                        </a>
                        <a class="dropdown-item d-flex align-items-center justify-content-between" href="{{ route('password.index') }}">
                            <span class="fs-sm fw-medium">Change Password</span>
                        </a>
                    </div>
                    <div role="separator" class="dropdown-divider m-0"></div>
                    <div class="p-2">
                        <a class="dropdown-item d-flex align-items-center justify-content-between" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <span class="fs-sm fw-medium">Log Out</span>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
