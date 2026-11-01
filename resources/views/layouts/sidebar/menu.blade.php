<ul class="nav-main">
    <li class="nav-main-item">
        <a class="nav-main-link {{ addAttrVal(['/', 'dashboard'], 'active') }}" href="{{ route('dashboard') }}">
            <i class="nav-main-link-icon si si-speedometer"></i>
            <span class="nav-main-link-name">Dashboard</span>
        </a>
    </li>

    @canany([App\Enums\PermissionEnum::USER_LIST, App\Enums\PermissionEnum::ROLE_LIST, App\Enums\PermissionEnum::GLOBAL_SETTING, App\Enums\PermissionEnum::HEALTH_MONITORING_VIEW])
    <li class="nav-main-heading">Administrator</li>

    @can(App\Enums\PermissionEnum::USER_LIST)
    <li class="nav-main-item">
        <a class="nav-main-link {{ addAttrVal(['users*'], 'active') }}" href="{{ route('users.index') }}">
            <i class="nav-main-link-icon si si-users"></i>
            <span class="nav-main-link-name">Users</span>
        </a>
    </li>
    @endcan

    @canany([App\Enums\PermissionEnum::ROLE_LIST, App\Enums\PermissionEnum::ROLE_CREATE])
    <li class="nav-main-item {{ addAttrVal(['roles*'], 'open') }}">
        <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="{{ addAttrVal(['roles*'], 'true', 'false') }}" href="#">
            <i class="nav-main-link-icon si si-shield"></i>
            <span class="nav-main-link-name">Roles</span>
        </a>
        <ul class="nav-main-submenu">
            @can(App\Enums\PermissionEnum::ROLE_LIST)
            <li class="nav-main-item">
                <a class="nav-main-link {{ addAttrVal(['roles'], 'active') }}" href="{{ route('roles.index') }}">
                    <i class="nav-main-link-icon far fa-circle font-size-7"></i>
                    <span class="nav-main-link-name">Role List</span>
                </a>
            </li>
            @endcan
            @can(App\Enums\PermissionEnum::ROLE_CREATE)
            <li class="nav-main-item">
                <a class="nav-main-link {{ addAttrVal(['roles/create'], 'active') }}" href="{{ route('roles.create') }}">
                    <i class="nav-main-link-icon far fa-circle font-size-7"></i>
                    <span class="nav-main-link-name">Role Create</span>
                </a>
            </li>
            @endcan
        </ul>
    </li>
    @endcanany

    @can(App\Enums\PermissionEnum::GLOBAL_SETTING)
    <li class="nav-main-item">
        <a class="nav-main-link {{ addAttrVal(['global-settings*'], 'active') }}" href="{{ route('global-setting.index') }}">
            <i class="nav-main-link-icon si si-settings"></i>
            <span class="nav-main-link-name">Global Setting</span>
        </a>
    </li>
    @endcan

    @can(App\Enums\PermissionEnum::HEALTH_MONITORING_VIEW)
    <li class="nav-main-item">
        <a class="nav-main-link {{ addAttrVal(['health-monitoring*'], 'active') }}" href="{{ route('health-monitoring.index') }}">
            <i class="nav-main-link-icon si si-chemistry"></i>
            <span class="nav-main-link-name">Infrastructure Monitoring</span>
        </a>
    </li>
    @endcan

    @can('viewLogViewer')
    <li class="nav-main-item">
        <a class="nav-main-link {{ addAttrVal(['logs*'], 'active') }}" href="{{ url('logs') }}" target="_blank">
            <i class="nav-main-link-icon si si-doc"></i>
            <span class="nav-main-link-name">Logs</span>
        </a>
    </li>
    @endcan

    @endcanany
</ul>
