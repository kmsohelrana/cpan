<x-app-layout title="{{ $title }}">

    <x-slot name="styles">
        <style>
            .stat-icon {
                width: 48px;
                height: 48px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.2rem;
                flex-shrink: 0;
            }

            .chart-wrap {
                position: relative;
                height: 220px;
            }

            .status-timeline {
                display: flex;
                gap: 3px;
                flex-wrap: nowrap;
                overflow-x: auto;
            }

            .status-block {
                width: 14px;
                height: 22px;
                border-radius: 3px;
                flex-shrink: 0;
            }

            [id^="section-"] {
                scroll-margin-top: 8rem; /* fixed #page-header (4rem) + sticky .section-nav height */
            }

            .section-nav {
                position: sticky;
                top: 4rem; /* sits just below the app's fixed #page-header (z-index 1030), not behind it */
                z-index: 20;
            }

            .log-summary-pie-wrap {
                position: relative;
                height: 160px;
                width: 160px;
                flex-shrink: 0;
            }
        </style>
    </x-slot>

    <x-header title="Infrastructure Monitoring">
        <li class="breadcrumb-item">Infrastructure Monitoring</li>
    </x-header>

    {{-- ===== Section jump nav + help ===== --}}
    <div class="block block-rounded section-nav mb-4">
        <div class="block-content d-flex align-items-center flex-wrap gap-2 py-2">
            @foreach ([
                'section-active-sessions' => 'Active Sessions',
                'section-log-summary' => "Today's Log",
                'section-directory-storage' => 'Directory Storage',
                'section-api-traffic' => 'API Traffic',
                'section-outbound-connectivity' => 'Outbound Connectivity',
                'section-summary-cards' => 'Summary Cards',
                'section-detail-panels' => 'Detail Panels',
                'section-charts' => 'Charts',
                'section-status-history' => 'Status History',
                'section-recent-events' => 'Recent Events',
            ] as $anchor => $label)
                <a href="#{{ $anchor }}" class="btn btn-sm btn-alt-secondary">{{ $label }}</a>
            @endforeach

            <button type="button" id="download-dashboard-png" class="btn btn-sm btn-outline-secondary rounded-circle ms-auto d-inline-flex align-items-center justify-content-center p-0" style="width: 2rem; height: 2rem;" title="Download PNG" aria-label="Download PNG">
                <i class="fa fa-download"></i>
            </button>

            <button type="button" class="btn btn-sm btn-outline-primary rounded-circle d-inline-flex align-items-center justify-content-center p-0" style="width: 2rem; height: 2rem;" data-bs-toggle="modal" data-bs-target="#health-monitoring-help-modal" title="What is this page?" aria-label="What is this page?">
                <i class="fa fa-info"></i>
            </button>
        </div>
    </div>

    <div id="dashboard-capture-area">
    <x-modal title="health-monitoring-help-modal" size="lg">
        <div class="block block-rounded block-transparent mb-0">
            <div class="block-header block-header-default">
                <h3 class="block-title"><i class="fa fa-circle-info me-1 text-primary"></i> About This Page</h3>
                <div class="block-options">
                    <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa fa-fw fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="block-content fs-sm pb-4">
                <p>The Infrastructure Monitoring dashboard gives system administrators a single place to check the health of the application, its infrastructure, and how it's being used — without needing server or database access.</p>
                <dl class="row">
                    <dt class="col-12 col-md-4">Active Sessions</dt>
                    <dd class="col-12 col-md-8">Who is currently logged into this admin panel, from which IP address, on what device, and how long since they were last active.</dd>

                    <dt class="col-12 col-md-4">Today's Log Summary</dt>
                    <dd class="col-12 col-md-8">How many entries of each severity (emergency/critical/alert, error, warning, notice/info/debug) have been written to today's application log file.</dd>

                    <dt class="col-12 col-md-4">Directory Storage Breakdown</dt>
                    <dd class="col-12 col-md-8">Which folders (vendor, storage/logs, storage/app, etc.) are using the most disk space, to help track down what's filling up the server.</dd>

                    <dt class="col-12 col-md-4">API Request Traffic</dt>
                    <dd class="col-12 col-md-8">How many CRM app / Merchant app mobile API requests have hit the server per hour over the last 24 hours, and which routes are busiest.</dd>

                    <dt class="col-12 col-md-4">Summary Cards</dt>
                    <dd class="col-12 col-md-8">A quick status badge for each monitored component: Application, Server, Database, Storage, Queue, Redis, and Mail.</dd>

                    <dt class="col-12 col-md-4">Detail Panels</dt>
                    <dd class="col-12 col-md-8">The full detail behind each summary card — e.g. database version and response time, queue driver and pending job count, server hostname and memory usage.</dd>

                    <dt class="col-12 col-md-4">Charts</dt>
                    <dd class="col-12 col-md-8">Trend lines for storage usage, database response time, queue size, and memory usage over recent collection runs.</dd>

                    <dt class="col-12 col-md-4">Component Status History</dt>
                    <dd class="col-12 col-md-8">A color-coded timeline strip per component (green/yellow/red) so you can spot at a glance when something went unhealthy.</dd>

                    <dt class="col-12 col-md-4">Recent Health Events</dt>
                    <dd class="col-12 col-md-8">A log of every health check result from the last 24 hours, paginated 20 rows at a time.</dd>
                </dl>
                <p class="text-muted mb-0">Most sections refresh automatically every {{ config('health_monitoring.refresh_interval_minutes') }} minutes via a scheduled background job — reloading this page does not run new checks itself. Active Sessions, API Traffic, and the Log Summary read live/near-live data instead, since staleness there would be misleading.</p>
            </div>
        </div>
    </x-modal>

    {{-- ===== Active Sessions (live, not cached) ===== --}}
    <div id="section-active-sessions" class="row g-3 mb-4">
        <div class="col-12">
            <div class="block block-rounded mb-0">
                <div class="block-header block-header-default">
                    <h3 class="block-title"><i class="fa fa-users me-2"></i> Active Sessions ({{ $activeSessions->total() }})</h3>
                </div>
                <div class="block-content">
                    <div class="table-responsive">
                        <table class="table table-bordered table-vcenter">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>IP Address</th>
                                    <th>Device / Browser</th>
                                    <th>Last Active</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($activeSessions as $session)
                                    <tr>
                                        <td>
                                            {{ $session['name'] }} <span class="text-muted fs-xs">({{ $session['email'] }})</span>
                                            @if ($session['is_current'])
                                                <span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-primary-light text-primary">You</span>
                                            @endif
                                        </td>
                                        <td>{{ $session['ip_address'] }}</td>
                                        <td class="text-truncate" style="max-width: 260px;" title="{{ $session['user_agent'] }}">{{ $session['user_agent'] }}</td>
                                        <td title="{{ $session['last_active_at'] }}">{{ $session['last_active'] }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center">No active sessions</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $activeSessions->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Today's Log Summary (live, 30s cache) ===== --}}
    <div id="section-log-summary" class="row g-3 mb-4">
        <div class="col-12">
            <div class="block block-rounded mb-0">
                <div class="block-header block-header-default">
                    <h3 class="block-title"><i class="fa fa-file-lines me-2"></i> Today's Log Summary</h3>
                </div>
                <div class="block-content pb-3">
                    @if (!$logSummary['exists'])
                        <div class="text-muted fs-sm">No log file found for today yet.</div>
                    @else
                        @php
                            $lc = $logSummary['counts'];
                            $levelColors = [
                                'emergency' => 'danger', 'alert' => 'danger', 'critical' => 'danger',
                                'error' => 'danger', 'warning' => 'warning',
                                'notice' => 'secondary', 'info' => 'secondary', 'debug' => 'secondary',
                            ];
                        @endphp
                        <div class="d-flex flex-wrap align-items-center gap-4">
                            <div class="log-summary-pie-wrap">
                                <canvas id="chart-log-summary-pie"
                                    data-values="{{ implode(',', array_values($lc)) }}"></canvas>
                            </div>
                            <div class="d-flex flex-wrap gap-4">
                                @foreach ($levelColors as $level => $color)
                                    <div>
                                        <div class="fs-xs text-muted text-capitalize">{{ $level }}</div>
                                        <div class="fs-4 fw-bold text-{{ $color }}">{{ $lc[$level] }}</div>
                                    </div>
                                @endforeach
                                <div>
                                    <div class="fs-xs text-muted">Total Entries</div>
                                    <div class="fs-4 fw-bold">{{ $logSummary['total'] }}</div>
                                </div>
                                <div>
                                    <div class="fs-xs text-muted">File Size</div>
                                    <div class="fs-4 fw-bold">{{ $logSummary['file_size_kb'] }} KB</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Directory Storage Breakdown (cached with the scheduled collection run) ===== --}}
    <div id="section-directory-storage" class="row g-3 mb-4">
        <div class="col-12">
            <div class="block block-rounded mb-0">
                <div class="block-header block-header-default">
                    <h3 class="block-title"><i class="fa fa-folder-tree me-2"></i> Directory Storage Breakdown</h3>
                </div>
                <div class="block-content">
                    @if (!$directorySizes)
                        <div class="text-muted fs-sm">Waiting for the first metrics collection run.</div>
                    @elseif (empty($directorySizes))
                        <div class="text-muted fs-sm">None of the known directories were found.</div>
                    @else
                        @php $largestMb = max(array_column($directorySizes, 'size_mb')) ?: 1; @endphp
                        @foreach ($directorySizes as $dir)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between fs-sm mb-1">
                                    <span>{{ $dir['label'] }} <span class="text-muted">({{ $dir['path'] }})</span></span>
                                    <span class="fw-semibold">{{ $dir['size_mb'] >= 1024 ? round($dir['size_mb'] / 1024, 2) . ' GB' : $dir['size_mb'] . ' MB' }}</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-primary" style="width: {{ round(($dir['size_mb'] / $largestMb) * 100, 1) }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ===== API Request Traffic (live, last 24h) ===== --}}
    <div id="section-api-traffic" class="row g-3 mb-4">
        <div class="col-12">
            <div class="block block-rounded mb-0">
                <div class="block-header block-header-default">
                    <h3 class="block-title"><i class="fa fa-chart-line me-2"></i> API Requests by App (24h)</h3>
                </div>
                <div class="block-content">
                    <div class="chart-wrap">
                        <canvas id="chart-api-by-app"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="block block-rounded mb-0">
                <div class="block-header block-header-default">
                    <h3 class="block-title"><i class="fa fa-chart-line me-2"></i> API Requests by Route (24h)</h3>
                </div>
                <div class="block-content">
                    <div class="chart-wrap">
                        <canvas id="chart-api-by-route"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Outbound Connectivity (live "telnet check" per external integration) ===== --}}
    <div id="section-outbound-connectivity" class="row g-3 mb-4">
        <div class="col-12">
            <div class="block block-rounded mb-0">
                <div class="block-header block-header-default">
                    <h3 class="block-title"><i class="fa fa-tower-broadcast me-2"></i> Outbound Connectivity</h3>
                    <div class="block-options">
                        <button type="button" id="refresh-outbound-connectivity" class="btn btn-sm btn-alt-secondary">
                            <i class="fa fa-rotate-right me-1"></i> Recheck
                        </button>
                    </div>
                </div>
                <div class="block-content">
                    <div class="text-center py-3" id="outbound-connectivity-loading">
                        <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
                        <span class="fs-sm text-muted ms-2">Checking outbound connections...</span>
                    </div>
                    <div class="table-responsive d-none" id="outbound-connectivity-table-wrap">
                        <table class="table table-bordered table-vcenter">
                            <thead>
                                <tr>
                                    <th>Service</th>
                                    <th>Target</th>
                                    <th>Status</th>
                                    <th>Response Time</th>
                                    <th>Checked At</th>
                                </tr>
                            </thead>
                            <tbody id="outbound-connectivity-tbody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (!$summary)
        <div class="alert alert-info">Waiting for the first metrics collection run. This page reads only cached data — the <code>health:collect-metrics</code> scheduled command (every {{ config('health_monitoring.refresh_interval_minutes') }} minutes) fills it in.</div>
    @else
        @php
            $results = $summary['results'];
            $collectedAt = \Illuminate\Support\Carbon::parse($summary['collected_at']);
        @endphp

        <div class="text-muted fs-sm mb-3">Last updated: {{ $collectedAt->diffForHumans() }} ({{ $collectedAt->toDateTimeString() }})</div>

        {{-- ===== Summary Cards ===== --}}
        <div id="section-summary-cards" class="row g-3 mb-4">
            @foreach ($results as $component => $result)
                <div class="col-md-6 col-xl-4">
                    <div class="block block-rounded mb-0 h-100">
                        <div class="block-content d-flex align-items-center gap-3 py-3 px-3">
                            <div class="stat-icon" style="background:rgba(var(--bs-{{ $result['status']->color() }}-rgb),.1);color:var(--bs-{{ $result['status']->color() }});">
                                <i class="fa fa-{{ match($component) {
                                    'application' => 'gears',
                                    'server' => 'server',
                                    'database' => 'database',
                                    'storage' => 'hard-drive',
                                    'queue' => 'list-check',
                                    'redis' => 'bolt',
                                    'mail' => 'envelope',
                                } }}"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-capitalize">{{ App\Enums\HealthComponentEnum::from($component)->label() }}</div>
                                <div>{!! $result['status']->badge() !!}</div>
                                <div class="fs-xs text-muted mt-1">{{ $result['message'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ===== Detail Panels (Storage / Server / Database / Queue / Redis / Mail / Application) ===== --}}
        <div id="section-detail-panels" class="row g-3 mb-4">
            <div class="col-md-6 col-xl-4">
                <div class="block block-rounded mb-0 h-100">
                    <div class="block-header block-header-default">
                        <h3 class="block-title"><i class="fa fa-hard-drive me-2"></i> Storage</h3>
                    </div>
                    <div class="block-content">
                        @php $storage = $results['storage']['payload']; @endphp
                        <div class="progress mb-2" style="height: 10px;">
                            @if ($storage['progress_color'] === 'orange')
                                <div class="progress-bar" style="background-color: var(--bs-orange); width: {{ $storage['usage_percent'] }}%"></div>
                            @else
                                <div class="progress-bar bg-{{ $storage['progress_color'] }}" style="width: {{ $storage['usage_percent'] }}%"></div>
                            @endif
                        </div>
                        <div class="fs-sm text-muted mb-3">{{ $storage['usage_percent'] }}% used — {{ $storage['used_gb'] }} GB of {{ $storage['total_gb'] }} GB ({{ $storage['free_gb'] }} GB free)</div>
                        <dl class="row mb-0 fs-sm">
                            <dt class="col-6">Health Status</dt><dd class="col-6">{!! $results['storage']['status']->badge() !!}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-4">
                <div class="block block-rounded mb-0 h-100">
                    <div class="block-header block-header-default">
                        <h3 class="block-title"><i class="fa fa-server me-2"></i> Server Information</h3>
                    </div>
                    <div class="block-content">
                        @php $server = $results['server']['payload']; @endphp
                        <dl class="row mb-0 fs-sm">
                            <dt class="col-5">Hostname</dt><dd class="col-7">{{ $server['hostname'] }}</dd>
                            <dt class="col-5">Operating System</dt><dd class="col-7">{{ $server['os'] }}</dd>
                            <dt class="col-5">Server Time</dt><dd class="col-7">{{ $server['server_time'] }}</dd>
                            <dt class="col-5">Load Average</dt><dd class="col-7">{{ $server['load_average'] ? implode(', ', $server['load_average']) : 'Unavailable' }}</dd>
                            <dt class="col-5">Memory Usage</dt><dd class="col-7">{{ $server['memory_usage_percent'] !== null ? $server['memory_usage_percent'] . '% (' . $server['memory_usage_mb'] . ' MB PHP process)' : 'Unavailable' }}</dd>
                            <dt class="col-5">Disk Usage</dt><dd class="col-7">{{ $server['disk_used_percent'] !== null ? $server['disk_used_percent'] . '%' : 'Unavailable' }}</dd>
                            <dt class="col-5">PHP Version</dt><dd class="col-7">{{ $server['php_version'] }}</dd>
                            <dt class="col-5">Laravel Version</dt><dd class="col-7">{{ $server['laravel_version'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-4">
                <div class="block block-rounded mb-0 h-100">
                    <div class="block-header block-header-default">
                        <h3 class="block-title"><i class="fa fa-database me-2"></i> Database Health</h3>
                    </div>
                    <div class="block-content">
                        @php $db = $results['database']['payload']; @endphp
                        <dl class="row mb-0 fs-sm">
                            <dt class="col-6">Connection</dt><dd class="col-6">{{ $db['connected'] ? 'Connected' : 'Failed' }}</dd>
                            <dt class="col-6">Database</dt><dd class="col-6">{{ $db['database_name'] }}</dd>
                            <dt class="col-6">Version</dt><dd class="col-6">{{ $db['database_version'] }}</dd>
                            <dt class="col-6">Response Time</dt><dd class="col-6">{{ $db['response_time_ms'] }} ms</dd>
                            <dt class="col-6">DB Size</dt><dd class="col-6">{{ $db['database_size_mb'] !== null ? $db['database_size_mb'] . ' MB' : 'N/A' }}</dd>
                            <dt class="col-6">Active Connections</dt><dd class="col-6">{{ $db['active_connections'] ?? 'N/A' }}</dd>
                            <dt class="col-6">Slow Queries</dt><dd class="col-6">{{ $db['slow_queries'] ?? 'N/A' }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-4">
                <div class="block block-rounded mb-0 h-100">
                    <div class="block-header block-header-default">
                        <h3 class="block-title"><i class="fa fa-list-check me-2"></i> Queue Monitoring</h3>
                    </div>
                    <div class="block-content">
                        @php $queue = $results['queue']['payload']; @endphp
                        <dl class="row mb-0 fs-sm">
                            <dt class="col-7">Driver</dt><dd class="col-5">{{ $queue['driver'] }}</dd>
                            <dt class="col-7">Pending Jobs</dt><dd class="col-5">{{ $queue['pending_jobs'] ?? 'N/A' }}</dd>
                            <dt class="col-7">Failed Jobs</dt><dd class="col-5">{{ $queue['failed_jobs'] }}</dd>
                            <dt class="col-7">Running Workers</dt><dd class="col-5">{{ $queue['running_workers'] }}</dd>
                            <dt class="col-7">Scheduler Status</dt><dd class="col-5">{{ $queue['scheduler_status'] }}</dd>
                            <dt class="col-7">Supervisor Status</dt><dd class="col-5">{{ $queue['supervisor_status'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-4">
                <div class="block block-rounded mb-0 h-100">
                    <div class="block-header block-header-default">
                        <h3 class="block-title"><i class="fa fa-bolt me-2"></i> Redis</h3>
                    </div>
                    <div class="block-content">
                        @php $redis = $results['redis']['payload']; @endphp
                        <dl class="row mb-0 fs-sm">
                            <dt class="col-7">Connection</dt><dd class="col-5">{{ $redis['connected'] ? 'Connected' : 'Not configured' }}</dd>
                            <dt class="col-7">Ping</dt><dd class="col-5">{{ $redis['ping'] ?? 'N/A' }}</dd>
                            <dt class="col-7">Response Time</dt><dd class="col-5">{{ $redis['response_time_ms'] !== null ? $redis['response_time_ms'] . ' ms' : 'N/A' }}</dd>
                            <dt class="col-7">Memory Usage</dt><dd class="col-5">{{ $redis['memory_usage'] ?? 'N/A' }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-4">
                <div class="block block-rounded mb-0 h-100">
                    <div class="block-header block-header-default">
                        <h3 class="block-title"><i class="fa fa-envelope me-2"></i> Mail</h3>
                    </div>
                    <div class="block-content">
                        @php $mail = $results['mail']['payload']; @endphp
                        <dl class="row mb-0 fs-sm">
                            <dt class="col-7">Mailer</dt><dd class="col-5">{{ $mail['mailer'] }}</dd>
                            <dt class="col-7">Connection</dt><dd class="col-5">{{ $mail['connected'] === null ? 'N/A' : ($mail['connected'] ? 'Connected' : 'Failed') }}</dd>
                            <dt class="col-7">Host</dt><dd class="col-5">{{ $mail['host'] ?? 'N/A' }}</dd>
                            <dt class="col-7">Port</dt><dd class="col-5">{{ $mail['port'] ?? 'N/A' }}</dd>
                            <dt class="col-7">Response Time</dt><dd class="col-5">{{ $mail['response_time_ms'] !== null ? $mail['response_time_ms'] . ' ms' : 'N/A' }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-4">
                <div class="block block-rounded mb-0 h-100">
                    <div class="block-header block-header-default">
                        <h3 class="block-title"><i class="fa fa-gears me-2"></i> Application Information</h3>
                    </div>
                    <div class="block-content">
                        @php $app = $results['application']['payload']; @endphp
                        <dl class="row mb-0 fs-sm">
                            <dt class="col-7">App Version</dt><dd class="col-5">{{ $app['app_version'] }}</dd>
                            <dt class="col-7">Laravel Version</dt><dd class="col-5">{{ $app['laravel_version'] }}</dd>
                            <dt class="col-7">PHP Version</dt><dd class="col-5">{{ $app['php_version'] }}</dd>
                            <dt class="col-7">Timezone</dt><dd class="col-5">{{ $app['timezone'] }}</dd>
                            <dt class="col-7">Cache Driver</dt><dd class="col-5">{{ $app['cache_driver'] }}</dd>
                            <dt class="col-7">Session Driver</dt><dd class="col-5">{{ $app['session_driver'] }}</dd>
                            <dt class="col-7">Queue Driver</dt><dd class="col-5">{{ $app['queue_driver'] }}</dd>
                            <dt class="col-7">APP_ENV</dt><dd class="col-5">{{ $app['app_env'] }}</dd>
                            <dt class="col-7">APP_DEBUG</dt><dd class="col-5">{{ $app['app_debug'] ? 'true' : 'false' }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== Charts ===== --}}
        <div id="section-charts" class="row g-3 mb-4">
            @foreach ([
                'storage' => ['label' => 'Storage Usage (%)', 'icon' => 'hard-drive', 'color' => '13,110,253'],
                'database' => ['label' => 'DB Response Time (ms)', 'icon' => 'database', 'color' => '25,135,84'],
                'queue' => ['label' => 'Queue Pending Jobs', 'icon' => 'list-check', 'color' => '255,193,7'],
                'server' => ['label' => 'Memory Usage (%)', 'icon' => 'server', 'color' => '220,53,69'],
            ] as $chartComponent => $chartMeta)
                <div class="col-md-6 col-xl-4">
                    <div class="block block-rounded mb-0 h-100">
                        <div class="block-header block-header-default">
                            <h3 class="block-title"><i class="fa fa-{{ $chartMeta['icon'] }} me-2"></i> {{ $chartMeta['label'] }}</h3>
                        </div>
                        <div class="block-content">
                            <div class="chart-wrap">
                                <canvas id="chart-{{ $chartComponent }}" data-color="{{ $chartMeta['color'] }}"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ===== Component Status History ===== --}}
        <div id="section-status-history" class="row g-3 mb-4">
            <div class="col-12">
                <div class="block block-rounded mb-0">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Component Status History</h3>
                    </div>
                    <div class="block-content">
                        @foreach ($results as $component => $result)
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="text-capitalize fw-semibold" style="width: 100px; flex-shrink: 0;">{{ App\Enums\HealthComponentEnum::from($component)->label() }}</div>
                                <div class="status-timeline" id="status-timeline-{{ $component }}"></div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    @endif

    {{-- ===== Recent Health Events (live, last 24 hours, paginated) ===== --}}
    <div id="section-recent-events" class="row g-3 mb-4">
        <div class="col-12">
            <div class="block block-rounded mb-0">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Recent Health Events <span class="text-muted fs-sm fw-normal">(last 24 hours)</span></h3>
                </div>
                <div class="block-content">
                    <div class="table-responsive">
                        <table class="table table-bordered table-vcenter">
                            <thead>
                                <tr>
                                    <th>Timestamp</th>
                                    <th>Component</th>
                                    <th>Status</th>
                                    <th>Message</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentEvents as $event)
                                    <tr>
                                        <td>{{ $event->recorded_at->toDateTimeString() }}</td>
                                        <td class="text-capitalize">{{ $event->component }}</td>
                                        <td>{!! $event->status_badge !!}</td>
                                        <td>{{ $event->message }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center">No events in the last 24 hours</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $recentEvents->links() }}
                </div>
            </div>
        </div>
    </div>

    </div> {{-- /#dashboard-capture-area --}}

    <x-slot name="scripts">
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
        <script>
            $(function() {
                // ── Outbound Connectivity — live "telnet check" per external integration ──
                function loadOutboundConnectivity() {
                    $('#outbound-connectivity-loading').removeClass('d-none');
                    $('#outbound-connectivity-table-wrap').addClass('d-none');

                    $.ajax({
                        url: '{{ route("health-monitoring.outbound-connectivity") }}'
                        , success: function(res) {
                            const rows = res.data;
                            $('#outbound-connectivity-loading').addClass('d-none');
                            $('#outbound-connectivity-table-wrap').removeClass('d-none');

                            if (!rows.length) {
                                $('#outbound-connectivity-tbody').html('<tr><td colspan="5" class="text-center">No outbound integrations to check</td></tr>');
                                return;
                            }

                            let html = '';
                            $.each(rows, function(i, row) {
                                let statusBadge;
                                if (row.connected === null) {
                                    statusBadge = '<span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-info-light text-info">Not configured</span>';
                                } else if (row.connected) {
                                    statusBadge = '<span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-success-light text-success">Reachable</span>';
                                } else {
                                    statusBadge = '<span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-danger-light text-danger">' + (row.error || 'Unreachable') + '</span>';
                                }

                                html += '<tr>' +
                                    '<td>' + row.label + '</td>' +
                                    '<td>' + (row.target || 'N/A') + '</td>' +
                                    '<td>' + statusBadge + '</td>' +
                                    '<td>' + (row.response_time_ms !== null ? row.response_time_ms + ' ms' : 'N/A') + '</td>' +
                                    '<td>' + row.checked_at + '</td>' +
                                    '</tr>';
                            });
                            $('#outbound-connectivity-tbody').html(html);
                        }
                        , error: function() {
                            $('#outbound-connectivity-loading').addClass('d-none');
                            $('#outbound-connectivity-table-wrap').removeClass('d-none');
                            $('#outbound-connectivity-tbody').html('<tr><td colspan="5" class="text-center text-danger">Could not load outbound connectivity checks</td></tr>');
                        }
                    , });
                }

                loadOutboundConnectivity();
                $('#refresh-outbound-connectivity').on('click', loadOutboundConnectivity);

                // ── Download current view as PNG, stamped with the export date/time ──
                $('#download-dashboard-png').on('click', function() {
                    const button = $(this);
                    const originalHtml = button.html();
                    button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

                    html2canvas(document.getElementById('dashboard-capture-area'), {
                        useCORS: true
                        , scale: window.devicePixelRatio || 1
                        , backgroundColor: '#f5f7fa'
                    }).then(function(canvas) {
                        const stamp = new Date().toLocaleString();
                        const ctx = canvas.getContext('2d');
                        const barHeight = 36;

                        ctx.fillStyle = 'rgba(255,255,255,.92)';
                        ctx.fillRect(0, canvas.height - barHeight, canvas.width, barHeight);
                        ctx.fillStyle = '#333';
                        ctx.font = '14px sans-serif';
                        ctx.textBaseline = 'middle';
                        ctx.fillText('Infrastructure Monitoring — exported ' + stamp, 12, canvas.height - (barHeight / 2));

                        const link = document.createElement('a');
                        link.download = 'infrastructure-monitoring-' + new Date().toISOString().replace(/[:.]/g, '-') + '.png';
                        link.href = canvas.toDataURL('image/png');
                        link.click();

                        button.prop('disabled', false).html(originalHtml);
                    }).catch(function() {
                        button.prop('disabled', false).html(originalHtml);
                        alert('Could not capture the dashboard as an image. Please try again.');
                    });
                });

                // ── Today's Log Summary pie — data is already server-rendered, no AJAX needed ──
                const logPieCanvas = $('#chart-log-summary-pie');
                if (logPieCanvas.length) {
                    const values = logPieCanvas.data('values').toString().split(',').map(Number);

                    new Chart(logPieCanvas[0], {
                        type: 'pie'
                        , data: {
                            labels: ['Emergency', 'Alert', 'Critical', 'Error', 'Warning', 'Notice', 'Info', 'Debug']
                            , datasets: [{
                                data: values
                                , backgroundColor: [
                                    'rgb(220,53,69)', 'rgb(176,42,55)', 'rgb(133,32,42)'
                                    , 'rgb(253,126,20)', 'rgb(255,193,7)'
                                    , 'rgb(108,117,125)', 'rgb(148,157,165)', 'rgb(188,197,205)'
                                ]
                                , borderWidth: 2
                                , borderColor: '#fff'
                            , }]
                        , }
                        , options: {
                            responsive: true
                            , maintainAspectRatio: false
                            , plugins: {
                                legend: {
                                    display: false
                                }
                                , tooltip: {
                                    callbacks: {
                                        label: ctx => ' ' + ctx.label + ': ' + ctx.parsed
                                    }
                                }
                            , }
                        , }
                    , });
                }

                // ── API Request Traffic — always rendered, independent of the health-metrics cache state ──
                const trafficPalette = ['13,110,253', '25,135,84', '220,53,69', '255,193,7', '111,66,193', '32,201,151', '253,126,20', '13,202,240'];

                function renderTrafficChart(canvasId, url) {
                    $.ajax({
                        url: url
                        , success: function(res) {
                            const labels = res.data.labels;
                            const series = res.data.series;

                            new Chart($(canvasId)[0], {
                                type: 'line'
                                , data: {
                                    labels: labels
                                    , datasets: Object.keys(series).map(function(name, i) {
                                        const rgb = trafficPalette[i % trafficPalette.length];
                                        return {
                                            label: name
                                            , data: series[name]
                                            , borderColor: 'rgb(' + rgb + ')'
                                            , backgroundColor: 'rgba(' + rgb + ',.1)'
                                            , fill: false
                                            , tension: 0.3
                                            , pointRadius: 1
                                        };
                                    })
                                , }
                                , options: {
                                    responsive: true
                                    , maintainAspectRatio: false
                                    , plugins: {
                                        legend: {
                                            display: true
                                            , position: 'bottom'
                                            , labels: {
                                                boxWidth: 10
                                                , font: {
                                                    size: 10
                                                }
                                            }
                                        }
                                    }
                                    , scales: {
                                        y: {
                                            beginAtZero: true
                                            , ticks: {
                                                precision: 0
                                            }
                                            , grid: {
                                                color: 'rgba(0,0,0,.06)'
                                            }
                                        }
                                        , x: {
                                            grid: {
                                                display: false
                                            }
                                        }
                                    , }
                                , }
                            , });
                        }
                    , });
                }

                renderTrafficChart('#chart-api-by-app', '{{ route("health-monitoring.api-traffic.by-app") }}');
                renderTrafficChart('#chart-api-by-route', '{{ route("health-monitoring.api-traffic.by-route") }}');

                if (!$('#chart-storage').length) {
                    return; // empty state — no health-metric charts to render yet
                }

                const chartUrls = {
                    application: '{{ route("health-monitoring.chart-data", ["component" => "application"]) }}'
                    , server: '{{ route("health-monitoring.chart-data", ["component" => "server"]) }}'
                    , database: '{{ route("health-monitoring.chart-data", ["component" => "database"]) }}'
                    , storage: '{{ route("health-monitoring.chart-data", ["component" => "storage"]) }}'
                    , queue: '{{ route("health-monitoring.chart-data", ["component" => "queue"]) }}'
                    , redis: '{{ route("health-monitoring.chart-data", ["component" => "redis"]) }}'
                    , mail: '{{ route("health-monitoring.chart-data", ["component" => "mail"]) }}'
                , };

                $.each(chartUrls, function(component, url) {
                    $.ajax({
                        url: url
                        , success: function(res) {
                            const points = res.data;
                            const canvas = $('#chart-' + component);

                            if (canvas.length) {
                                const rgb = canvas.data('color');

                                new Chart(canvas[0], {
                                    type: 'line'
                                    , data: {
                                        labels: points.map(p => p.recorded_at)
                                        , datasets: [{
                                            data: points.map(p => p.value)
                                            , borderColor: 'rgb(' + rgb + ')'
                                            , backgroundColor: 'rgba(' + rgb + ',.15)'
                                            , fill: true
                                            , tension: 0.3
                                            , pointRadius: 2
                                        , }]
                                    , }
                                    , options: {
                                        responsive: true
                                        , maintainAspectRatio: false
                                        , plugins: {
                                            legend: {
                                                display: false
                                            }
                                        }
                                        , scales: {
                                            y: {
                                                beginAtZero: true
                                                , grid: {
                                                    color: 'rgba(0,0,0,.06)'
                                                }
                                            }
                                            , x: {
                                                grid: {
                                                    display: false
                                                }
                                            }
                                        , }
                                    , }
                                , });
                            }

                            const timeline = $('#status-timeline-' + component);
                            let blocks = '';
                            $.each(points, function(i, p) {
                                blocks += '<div class="status-block bg-' + p.color + '" title="' + p.recorded_at + ' — ' + p.message + '"></div>';
                            });
                            timeline.html(blocks || '<span class="fs-sm text-muted">No history yet</span>');
                        }
                    , });
                });
            });
        </script>
    </x-slot>

</x-app-layout>
