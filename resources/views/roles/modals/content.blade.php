<div class="mb-3">
    <strong class="text-primary">{{ $role->name }}</strong>
    <span class="ms-2 fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-primary-light text-light">{{ $role->permissions->count() }} Permissions</span>
</div>

@php
    $webGroups = $platformPermissions['web'] ?? collect();
    $appGroups = $platformPermissions['app'] ?? collect();
@endphp

<ul class="nav nav-tabs mb-0" id="viewPermissionTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="view-web-tab" data-bs-toggle="tab" data-bs-target="#view-web-permissions" type="button" role="tab">
            <i class="fa fa-globe me-1"></i> Web
            <span class="badge bg-primary ms-1">{{ $webGroups->flatten()->count() }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="view-app-tab" data-bs-toggle="tab" data-bs-target="#view-app-permissions" type="button" role="tab">
            <i class="fa fa-mobile-screen me-1"></i> App
            <span class="badge bg-success ms-1">{{ $appGroups->flatten()->count() }}</span>
        </button>
    </li>
</ul>

<div class="tab-content border border-top-0 rounded-bottom" id="viewPermissionTabContent">

    {{-- Web --}}
    <div class="tab-pane fade show active p-2" id="view-web-permissions" role="tabpanel">
        @if($webGroups->isNotEmpty())
        <table class="table table-bordered table-vcenter mb-0">
            <thead>
                <tr>
                    <th style="width: 30%;">Module</th>
                    <th>Permissions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($webGroups as $module => $permissions)
                <tr>
                    <td class="text-muted fw-semibold text-nowrap">{{ $module }}</td>
                    <td>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach ($permissions as $permission)
                            <span class="badge bg-light text-dark border">{{ \App\Enums\PermissionEnum::tryFrom($permission->name)?->label() ?? \Illuminate\Support\Str::headline($permission->name) }}</span>
                            @endforeach
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-muted mb-0 p-2">No web permissions assigned.</p>
        @endif
    </div>

    {{-- App --}}
    <div class="tab-pane fade p-2" id="view-app-permissions" role="tabpanel">
        @if($appGroups->isNotEmpty())
        <table class="table table-bordered table-vcenter mb-0">
            <thead>
                <tr>
                    <th style="width: 30%;">Module</th>
                    <th>Permissions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($appGroups as $module => $permissions)
                <tr>
                    <td class="text-muted fw-semibold text-nowrap">{{ $module }}</td>
                    <td>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach ($permissions as $permission)
                            <span class="badge bg-light text-dark border">{{ \App\Enums\PermissionEnum::tryFrom($permission->name)?->label() ?? \Illuminate\Support\Str::headline($permission->name) }}</span>
                            @endforeach
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-muted mb-0 p-2">No app permissions assigned.</p>
        @endif
    </div>

</div>
