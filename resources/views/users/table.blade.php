<div class="block block-rounded table-block h-100 mb-0">
    <div class="block-header block-header-default">
        <h3 class="block-title">Total {{ $title }} <span class="badge bg-primary">{{ $users->total() }}</span></h3>
        <div class="block-options">
            <a class="btn btn-sm btn-primary" data-bs-toggle="collapse" href="#collapseFilter" role="button" aria-controls="collapseFilter">
                <i class="fa fa-filter"></i> Filter
            </a>
            @can(App\Enums\PermissionEnum::USER_CREATE)
            <a href="javascript:void(0)" class="ms-2 btn btn-sm btn-primary create-data" data-bs-toggle="modal" data-bs-target="#form-modal">
                <i class="si si-plus"></i> Add User
            </a>
            @endcan
        </div>
    </div>
    <div class="block-content mb-3">
        <div class="table-responsive">
            <table class="table table-bordered table-vcenter">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $row)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $row->name }}</td>
                        <td>{{ $row->roles->isNotEmpty() ? $row->roles->first()->name : 'N/A' }}</td>
                        <td>{{ $row->email }}</td>
                        <td>{{ $row->mobile ?? 'N/A' }}</td>
                        <td>{!! $row->status_badge !!}</td>
                        <td>
                            <div class="d-flex justify-content-center gap-1 align-items-center">
                                @can(App\Enums\PermissionEnum::USER_EDIT)
                                @php $rowRole = $row->roles->first()?->name; @endphp
                                @if(
                                    isSuperAdmin() ||
                                    (isAdmin() && $rowRole !== App\Enums\RoleEnum::SUPER_ADMIN->value) ||
                                    (!isSuperAdmin() && !isAdmin() && !in_array($rowRole, [App\Enums\RoleEnum::SUPER_ADMIN->value, App\Enums\RoleEnum::ADMIN->value]))
                                )
                                <button type="button" class="btn btn-icon btn-outline-primary update-data" data-bs-toggle="modal" title="Edit" data-bs-target="#form-modal" data-row="{{ $row->id }}">
                                    <i class="fa fa-fw fa-pencil-alt"></i>
                                </button>
                                @endif
                                @endcan
                                @can(App\Enums\PermissionEnum::USER_VIEW)
                                <button type="button" class="btn btn-icon btn-outline-info show-data" title="Details" data-bs-toggle="modal" data-bs-target="#show-modal" data-row="{{ $row->id }}">
                                    <i class="fa fa-fw fa-eye"></i>
                                </button>
                                @endcan
                                @can(App\Enums\PermissionEnum::USER_DELETE)
                                <button type="button" class="btn btn-icon btn-outline-danger delete-data" title="Delete" data-row="{{ $row->id }}">
                                    <i class="fa fa-fw fa-trash-alt"></i>
                                </button>
                                @endcan
                                @can(App\Enums\PermissionEnum::USER_PASSWORD_UPDATE)
                                <button type="button" class="btn btn-icon btn-outline-warning update-password" title="Update Password" data-row="{{ $row->id }}">
                                    <i class="fa fa-fw fa-key"></i>
                                </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No data found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $users->links() }}
        </div>
    </div>
</div>
