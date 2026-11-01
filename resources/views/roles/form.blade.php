<div class="row">
    <div class="col-12 col-md-4">
        <div class="mb-3">
            <label class="form-label" for="name">
                <strong>Name <span class="text-danger">*</span></strong>
            </label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $role->name ?? '') }}" placeholder="Enter name" {{ $role->is_changeable ?? 1 ? '' : 'readonly' }} required>
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-12 col-md-auto ms-auto">
        <div class="mb-3">
            <label class="form-label d-block">&nbsp;</label>
            <a href="{{ route('roles.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fa fa-circle-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="col-12">
        {{-- Tab Navigation --}}
        <ul class="nav nav-tabs mb-0" id="permissionTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="web-tab" data-bs-toggle="tab" data-bs-target="#web-permissions" type="button" role="tab" aria-controls="web-permissions" aria-selected="true">
                    <i class="fa fa-globe me-1"></i> Web Permissions
                    <span class="badge bg-primary ms-1" id="webBadge">0</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="app-tab" data-bs-toggle="tab" data-bs-target="#app-permissions" type="button" role="tab" aria-controls="app-permissions" aria-selected="false">
                    <i class="fa fa-mobile-screen me-1"></i> App Permissions
                    <span class="badge bg-success ms-1" id="appBadge">0</span>
                </button>
            </li>
        </ul>

        {{-- Tab Panels --}}
        <div class="tab-content border border-top-0 rounded-bottom p-3" id="permissionTabContent">

            {{-- Web Permissions --}}
            <div class="tab-pane fade show active" id="web-permissions" role="tabpanel" aria-labelledby="web-tab">
                @php $webGroups = $platformPermissions['web'] ?? collect(); @endphp
                @if($webGroups->isNotEmpty())
                    <div class="d-flex justify-content-start mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="selectAllWeb">
                            <label class="form-check-label fw-semibold" for="selectAllWeb">Select All</label>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-vcenter mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 200px;">Module</th>
                                    <th>Permissions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($webGroups as $module => $permissions)
                                <tr>
                                    <th class="text-nowrap">{{ $module }}</th>
                                    <td>
                                        @foreach ($permissions as $permission)
                                        <div class="d-inline-block bg-light text-dark py-1 px-2 rounded border my-1 mb-md-0">
                                            <label class="form-check mb-0" role="button">
                                                <input class="form-check-input web-permission" name="permissions[]" type="checkbox" value="{{ $permission->name }}"
                                                    {{ in_array($permission->name, old('permissions', $permissionNames ?? [])) ? 'checked' : '' }}>
                                                <span class="form-check-label me-2">
                                                    {{ \App\Enums\PermissionEnum::tryFrom($permission->name)?->label() ?? \Illuminate\Support\Str::headline($permission->name) }}
                                                </span>
                                            </label>
                                        </div>
                                        @endforeach
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted mb-0">No web permissions available.</p>
                @endif
            </div>

            {{-- App Permissions --}}
            <div class="tab-pane fade" id="app-permissions" role="tabpanel" aria-labelledby="app-tab">
                @php $appGroups = $platformPermissions['app'] ?? collect(); @endphp
                @if($appGroups->isNotEmpty())
                    <div class="d-flex justify-content-start mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="selectAllApp">
                            <label class="form-check-label fw-semibold" for="selectAllApp">Select All</label>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-vcenter mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 200px;">Module</th>
                                    <th>Permissions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($appGroups as $module => $permissions)
                                <tr>
                                    <th class="text-nowrap">{{ $module }}</th>
                                    <td>
                                        @foreach ($permissions as $permission)
                                        <div class="d-inline-block bg-light text-dark py-1 px-2 rounded border my-1 mb-md-0">
                                            <label class="form-check mb-0" role="button">
                                                <input class="form-check-input app-permission" name="permissions[]" type="checkbox" value="{{ $permission->name }}"
                                                    {{ in_array($permission->name, old('permissions', $permissionNames ?? [])) ? 'checked' : '' }}>
                                                <span class="form-check-label me-2">
                                                    {{ \App\Enums\PermissionEnum::tryFrom($permission->name)?->label() ?? \Illuminate\Support\Str::headline($permission->name) }}
                                                </span>
                                            </label>
                                        </div>
                                        @endforeach
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted mb-0">No app permissions available.</p>
                @endif
            </div>

        </div>{{-- end tab-content --}}
    </div>

    <div class="col-12">
        <div class="text-end mt-3 mb-3">
            <button type="submit" class="btn btn-sm btn-primary">
                <i class="fa fa-circle-plus"></i> Submit
            </button>
        </div>
    </div>
</div>

<script>
    function updateBadge(groupClass, badgeId) {
        const count = document.querySelectorAll('.' + groupClass + ':checked').length;
        const badge = document.getElementById(badgeId);
        if (badge) badge.textContent = count;
    }

    function syncSelectAll(groupClass, selectAllId) {
        const all = document.querySelectorAll('.' + groupClass);
        const checked = document.querySelectorAll('.' + groupClass + ':checked');
        const selectAllEl = document.getElementById(selectAllId);
        if (selectAllEl) selectAllEl.checked = all.length > 0 && all.length === checked.length;
    }

    // Select All – Web
    document.getElementById('selectAllWeb')?.addEventListener('change', function () {
        document.querySelectorAll('.web-permission').forEach(cb => cb.checked = this.checked);
        updateBadge('web-permission', 'webBadge');
    });

    // Select All – App
    document.getElementById('selectAllApp')?.addEventListener('change', function () {
        document.querySelectorAll('.app-permission').forEach(cb => cb.checked = this.checked);
        updateBadge('app-permission', 'appBadge');
    });

    document.querySelectorAll('.web-permission').forEach(cb => cb.addEventListener('change', () => {
        syncSelectAll('web-permission', 'selectAllWeb');
        updateBadge('web-permission', 'webBadge');
    }));

    document.querySelectorAll('.app-permission').forEach(cb => cb.addEventListener('change', () => {
        syncSelectAll('app-permission', 'selectAllApp');
        updateBadge('app-permission', 'appBadge');
    }));

    // Init on load
    syncSelectAll('web-permission', 'selectAllWeb');
    syncSelectAll('app-permission', 'selectAllApp');
    updateBadge('web-permission', 'webBadge');
    updateBadge('app-permission', 'appBadge');
</script>
