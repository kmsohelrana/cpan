<div class="block block-rounded table-block h-100 mb-0">
    <div class="block-header block-header-default">
        <h3 class="block-title">Total {{ $title }} <span class="badge bg-primary">{{ $notifications->total() }}</span></h3>
        <div class="block-options">
            <a class="btn btn-sm btn-primary" data-bs-toggle="collapse" href="#collapseFilter" role="button"
                aria-controls="collapseFilter">
                <i class="fa fa-filter"></i> Filter
            </a>
        </div>
    </div>
    <div class="block-content mb-3">
        <div class="table-responsive">
            <table class="table table-bordered table-vcenter">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Body</th>
                        <th>Received At</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($notifications as $row)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="{{ $row->read_at ? '' : 'fw-bold' }}">{{ $row->title }}</td>
                            <td class="{{ $row->read_at ? '' : 'fw-bold' }}">{{ $row->message }}</td>
                            <td class="{{ $row->read_at ? '' : 'fw-bold' }}">{{ $row->created_at->format('d M Y, h:i A') }}</td>
                            <td>
                                @if ($row->read_at)
                                    <span class="badge bg-success">Read</span>
                                @else
                                    <span class="badge bg-warning">Unread</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-1 align-items-center">
                                    @if (!$row->read_at)
                                        <button type="button" class="btn btn-icon btn-outline-primary mark-read-data"
                                            title="Mark as Read" data-row="{{ $row->id }}">
                                            <i class="fa fa-fw fa-check"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No notifications found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $notifications->links() }}
        </div>
    </div>
</div>
