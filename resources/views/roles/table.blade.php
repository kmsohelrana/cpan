<div class="block block-rounded table-block h-100 mb-0">
    <div class="block-header block-header-default">
        <h3 class="block-title">Total {{ $title }} <span class="badge bg-primary">{{ $roles->total() }}</span></h3>
        <div class="block-options">
            <div class="input-group input-group-sm input-table-header">
                <input type="search" class="form-control" placeholder="Search.." id="page-header-search-input2" name="page-header-search-input2" value="{{ $search }}">
                <button class="input-group-text" onclick="getDataList(1, $('#page-header-search-input2').val())">
                    <i class="fa fa-fw fa-search"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="block-content">
        <div class="table-responsive">
            <table class="table table-bordered table-vcenter">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Role Name</th>
                        <th>Permissions</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($roles as $role)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $role->name }}</td>
                        <td>
                            <span class="py-1 px-3 rounded-pill bg-success-light text-success me-1" title="Total">{{ $role->permissions_count }}</span>
                            <span class="py-1 px-3 rounded-pill bg-info-light text-info me-1" title="Web"><i class="fa fa-globe fa-xs me-1"></i>{{ $role->web_permissions_count }}</span>
                            <span class="py-1 px-3 rounded-pill bg-warning-light text-warning" title="App"><i class="fa fa-mobile-screen fa-xs me-1"></i>{{ $role->app_permissions_count }}</span>
                        </td>
                        <td>{{ $role->created_at?->format('d M Y') }}</td>
                        <td>{{ $role->updated_at?->format('d M Y') }}</td>
                        <td>
                            <div class="d-flex justify-content-center gap-1 align-items-center">
                                @can(App\Enums\PermissionEnum::ROLE_EDIT)
                                <a href="{{ route('roles.edit', $role) }}" class="btn btn-icon btn-outline-primary" title="Edit">
                                    <i class="fa fa-fw fa-pencil-alt"></i>
                                </a>
                                @endcan
                                @can(App\Enums\PermissionEnum::ROLE_VIEW)
                                <button type="button" class="btn btn-icon btn-outline-info show-data" title="View Permissions" data-bs-toggle="modal" data-bs-target="#show-modal" data-row="{{ $role->id }}">
                                    <i class="fa fa-fw fa-eye"></i>
                                </button>
                                @endcan
                                @can(App\Enums\PermissionEnum::ROLE_DELETE)
                                @if ($role->is_changeable)
                                <button type="button" class="btn btn-icon btn-outline-danger delete" data-bs-toggle="tooltip" title="Delete" data-title="Are you sure want to delete?" data-id="delete-{{ $role->id }}">
                                    <i class="fa fa-fw fa-trash-alt"></i>
                                </button>
                                <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-none" id="delete-{{ $role->id }}">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                @endif
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
            {{ $roles->links() }}
        </div>
    </div>
</div>
