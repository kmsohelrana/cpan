<table class="table table-bordered table-vcenter">
    <tbody>
        <tr>
            <th class="text-nowrap" style="width: 35%;">Profile</th>
            <td>
                <img src="{{ $user->image ?? asset('avatar.png') }}" alt="{{ $user->name }}" class="img-fluid rounded" style="max-height:60px">
            </td>
        </tr>
        <tr>
            <th class="text-nowrap">Name</th>
            <td><strong class="text-primary">{{ $user->name }}</strong></td>
        </tr>
        <tr>
            <th class="text-nowrap">Employee / Staff ID</th>
            <td>{{ $user->employee_id ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th class="text-nowrap">Role</th>
            <td class="text-success">{{ $user->roles->isNotEmpty() ? $user->roles->first()->name : 'N/A' }}</td>
        </tr>
        <tr>
            <th class="text-nowrap">Email</th>
            <td>{{ $user->email }}</td>
        </tr>
        <tr>
            <th class="text-nowrap">Mobile</th>
            <td>{{ $user->mobile ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th class="text-nowrap">Status</th>
            <td>{!! $user->status_badge !!}</td>
        </tr>
        <tr>
            <th class="text-nowrap">Created At</th>
            <td>{{ $user->created_at?->format('d M Y, h:i A') ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th class="text-nowrap">Updated At</th>
            <td>{{ $user->updated_at?->format('d M Y, h:i A') ?? 'N/A' }}</td>
        </tr>
    </tbody>
</table>
