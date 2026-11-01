<x-app-layout title="{{ $title }}">

    <x-slot name="styles">
        <style>
            .stat-icon {
                width: 52px;
                height: 52px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.35rem;
                flex-shrink: 0;
            }

            .stat-value {
                font-size: 1.65rem;
                font-weight: 700;
                line-height: 1.1;
            }

            .stat-label {
                font-size: .82rem;
                font-weight: 500;
                margin-top: 3px;
            }

            .stat-footer {
                font-size: .75rem;
                border-top: 1px solid rgba(0, 0, 0, .06);
                padding-top: .45rem;
                margin-top: .45rem;
            }

            .quick-action-card {
                transition: transform .15s ease, box-shadow .15s ease;
                cursor: pointer;
            }

            .quick-action-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 .5rem 1rem rgba(0,0,0,.08) !important;
            }
        </style>
    </x-slot>

    <x-header title="Administrator Dashboard">
        <li class="breadcrumb-item">Dashboard</li>
    </x-header>

    {{-- ===== 4 Main Stat Cards ===== --}}
    <div class="row g-3 mb-4">
        {{-- Users Card --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('users.index') }}" class="text-decoration-none">
                <div class="block block-rounded mb-0 h-100 quick-action-card">
                    <div class="block-content d-flex align-items-center gap-3 py-3 px-3">
                        <div class="stat-icon" style="background: rgba(13, 110, 253, .12); color: #0d6efd;">
                            <i class="si si-users"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="stat-value text-primary" id="stat-users-total">
                                <span class="spinner-border spinner-border-sm text-muted"></span>
                            </div>
                            <div class="stat-label text-muted">System Users</div>
                            <div class="stat-footer text-muted" id="stat-users-footer">
                                <span class="placeholder-glow"><span class="placeholder col-8 rounded"></span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Roles Card --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('roles.index') }}" class="text-decoration-none">
                <div class="block block-rounded mb-0 h-100 quick-action-card">
                    <div class="block-content d-flex align-items-center gap-3 py-3 px-3">
                        <div class="stat-icon" style="background: rgba(25, 135, 84, .12); color: #198754;">
                            <i class="si si-shield"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="stat-value text-success" id="stat-roles-total">
                                <span class="spinner-border spinner-border-sm text-muted"></span>
                            </div>
                            <div class="stat-label text-muted">Roles & Permissions</div>
                            <div class="stat-footer text-muted" id="stat-roles-footer">
                                Access Control & Permissions
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Global Settings Card --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('global-setting.index') }}" class="text-decoration-none">
                <div class="block block-rounded mb-0 h-100 quick-action-card">
                    <div class="block-content d-flex align-items-center gap-3 py-3 px-3">
                        <div class="stat-icon" style="background: rgba(111, 66, 193, .12); color: #6f42c1;">
                            <i class="si si-settings"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="stat-value" style="color: #6f42c1;" id="stat-settings-total">
                                <span class="spinner-border spinner-border-sm text-muted"></span>
                            </div>
                            <div class="stat-label text-muted">Global Settings</div>
                            <div class="stat-footer text-muted" id="stat-settings-footer">
                                System Preferences
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Infrastructure Health Card --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('health-monitoring.index') }}" class="text-decoration-none">
                <div class="block block-rounded mb-0 h-100 quick-action-card">
                    <div class="block-content d-flex align-items-center gap-3 py-3 px-3">
                        <div class="stat-icon" id="stat-health-icon" style="background: rgba(13, 202, 240, .12); color: #0dcaf0;">
                            <i class="si si-chemistry"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="stat-value" id="stat-health-status">
                                <span class="spinner-border spinner-border-sm text-muted"></span>
                            </div>
                            <div class="stat-label text-muted">Infrastructure Health</div>
                            <div class="stat-footer text-muted" id="stat-health-footer">
                                Real-time monitoring
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- ===== ROW 2 — Quick Navigation & System Overview ===== --}}
    <div class="row g-4 mb-4">
        {{-- Quick Navigation --}}
        <div class="col-12 col-lg-7">
            <div class="block block-rounded h-100 mb-0">
                <div class="block-header block-header-default">
                    <h3 class="block-title"><i class="si si-grid me-2 text-primary"></i>Administration Modules</h3>
                </div>
                <div class="block-content p-3">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <a href="{{ route('users.index') }}" class="text-decoration-none">
                                <div class="p-3 border rounded h-100 d-flex align-items-center gap-3 quick-action-card">
                                    <div class="stat-icon bg-body-light text-primary">
                                        <i class="si si-user-follow"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark">User Management</div>
                                        <div class="fs-xs text-muted">Manage system administrators & operators</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-6">
                            <a href="{{ route('roles.index') }}" class="text-decoration-none">
                                <div class="p-3 border rounded h-100 d-flex align-items-center gap-3 quick-action-card">
                                    <div class="stat-icon bg-body-light text-success">
                                        <i class="si si-shield"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark">Role Management</div>
                                        <div class="fs-xs text-muted">Configure system roles & permissions</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-6">
                            <a href="{{ route('global-setting.index') }}" class="text-decoration-none">
                                <div class="p-3 border rounded h-100 d-flex align-items-center gap-3 quick-action-card">
                                    <div class="stat-icon bg-body-light text-purple">
                                        <i class="si si-equalizer"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark">System Settings</div>
                                        <div class="fs-xs text-muted">Configure SMS, Email & App parameters</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-6">
                            <a href="{{ route('health-monitoring.index') }}" class="text-decoration-none">
                                <div class="p-3 border rounded h-100 d-flex align-items-center gap-3 quick-action-card">
                                    <div class="stat-icon bg-body-light text-info">
                                        <i class="si si-speedometer"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark">Health Monitoring</div>
                                        <div class="fs-xs text-muted">DB, Server, Storage & Queue status</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @can('viewLogViewer')
                        <div class="col-12">
                            <a href="{{ url('logs') }}" class="text-decoration-none" target="_blank">
                                <div class="p-3 border rounded h-100 d-flex align-items-center gap-3 quick-action-card">
                                    <div class="stat-icon bg-body-light text-danger">
                                        <i class="si si-doc"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark">System Logs</div>
                                        <div class="fs-xs text-muted">Inspect application logs and exceptions in real time</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        {{-- System Environment Overview --}}
        <div class="col-12 col-lg-5">
            <div class="block block-rounded h-100 mb-0">
                <div class="block-header block-header-default">
                    <h3 class="block-title"><i class="si si-info me-2 text-info"></i>Environment Details</h3>
                </div>
                <div class="block-content p-0">
                    <table class="table table-striped table-vcenter mb-0 fs-sm">
                        <tbody>
                            <tr>
                                <td class="fw-medium text-muted" style="width: 45%;">PHP Version</td>
                                <td><span class="badge bg-primary">{{ $systemInfo['php_version'] }}</span></td>
                            </tr>
                            <tr>
                                <td class="fw-medium text-muted">Laravel Framework</td>
                                <td><span class="badge bg-danger">{{ $systemInfo['laravel_version'] }}</span></td>
                            </tr>
                            <tr>
                                <td class="fw-medium text-muted">Environment</td>
                                <td>
                                    <span class="badge {{ $systemInfo['app_env'] === 'production' ? 'bg-success' : 'bg-warning' }}">
                                        {{ ucfirst($systemInfo['app_env']) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-medium text-muted">Database Engine</td>
                                <td><span class="badge bg-secondary">{{ strtoupper($systemInfo['db_driver']) }}</span></td>
                            </tr>
                            <tr>
                                <td class="fw-medium text-muted">Cache Driver</td>
                                <td><span class="badge bg-secondary">{{ strtoupper($systemInfo['cache_driver']) }}</span></td>
                            </tr>
                            <tr>
                                <td class="fw-medium text-muted">Queue Driver</td>
                                <td><span class="badge bg-secondary">{{ strtoupper($systemInfo['queue_connection']) }}</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <x-slot name="scripts">
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                fetch("{{ route('dashboard.counts') }}")
                    .then(response => response.json())
                    .then(data => {
                        // Users
                        if (data.users) {
                            document.getElementById('stat-users-total').textContent = data.users.total.toLocaleString();
                            document.getElementById('stat-users-footer').innerHTML =
                                `<span class="text-success"><i class="fa fa-circle fs-xs"></i> Active: ${data.users.active}</span> &bull; <span class="text-muted">Inactive: ${data.users.inactive}</span>`;
                        }

                        // Roles
                        if (data.roles) {
                            document.getElementById('stat-roles-total').textContent = data.roles.total.toLocaleString();
                        }

                        // Settings
                        if (data.settings) {
                            document.getElementById('stat-settings-total').textContent = data.settings.total.toLocaleString();
                        }

                        // Health
                        if (data.health) {
                            const healthEl = document.getElementById('stat-health-status');
                            const healthFooter = document.getElementById('stat-health-footer');
                            const healthIcon = document.getElementById('stat-health-icon');
                            
                            healthEl.textContent = data.health.status;
                            healthFooter.textContent = data.health.message;

                            if (data.health.status === 'OK') {
                                healthEl.className = 'stat-value text-success';
                                healthIcon.style.color = '#198754';
                                healthIcon.style.background = 'rgba(25, 135, 84, .12)';
                            } else if (data.health.status === 'WARNING') {
                                healthEl.className = 'stat-value text-warning';
                                healthIcon.style.color = '#ffc107';
                                healthIcon.style.background = 'rgba(255, 193, 7, .12)';
                            } else {
                                healthEl.className = 'stat-value text-danger';
                                healthIcon.style.color = '#dc3545';
                                healthIcon.style.background = 'rgba(220, 53, 69, .12)';
                            }
                        }
                    })
                    .catch(err => {
                        console.error('Failed to load dashboard metrics:', err);
                    });
            });
        </script>
    </x-slot>

</x-app-layout>
