<?php

namespace App\Http\Controllers;

use App\Enums\CommonEnum;
use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Http\Requests\Role\StoreRequest;
use App\Http\Requests\Role\UpdateRequest;
use Illuminate\Http\Request;
use App\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Response;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        checkAuthPermission(PermissionEnum::ROLE_LIST);
        $title = 'Roles';
        $data = $this->roleList($request);
        return view('roles.index', compact('title', 'data'));
    }

    /**
     * Display the data listing of the resource.
     */
    public function roleList(Request $request)
    {
        if (!userHasPermission(PermissionEnum::ROLE_LIST)) {
            return webResponse(Response::HTTP_UNAUTHORIZED, 'You do not have permission to get role list.');
        }
        $title = 'Roles';
        $search = $request->ajax() ? $request->search : '';
        $roles = Role::query()
            ->with('permissions')
            ->withCount([
                'permissions',
                'permissions as web_permissions_count' => fn($q) => $q->where('platform', 'web'),
                'permissions as app_permissions_count' => fn($q) => $q->where('platform', 'app'),
            ])
            ->where('guard_name', 'web')
            ->when(
                $request->filled('search'),
                fn($query) => $query->where('name', 'like', "%{$search}%")
            )->paginate(CommonEnum::PER_PAGE->value());

        return view('roles.table', compact('title', 'roles', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        checkAuthPermission(PermissionEnum::ROLE_CREATE);
        $title = 'Create Role';
        $validPermissionNames = array_column(PermissionEnum::cases(), 'value');
        $platformPermissions = Permission::query()
            ->where('guard_name', 'web')
            ->whereIn('name', $validPermissionNames)
            ->get()
            ->groupBy('platform')
            ->map(fn($perms) => $perms->groupBy('module_name'));

        $isSuperAdmin = false;
        return view('roles.create', compact('title', 'platformPermissions', 'isSuperAdmin'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        if (!userHasPermission(PermissionEnum::ROLE_VIEW)) {
            return webResponse(Response::HTTP_UNAUTHORIZED, 'You do not have permission to view this role.');
        }
        $role->load('permissions');
        $platformPermissions = $role->permissions
            ->groupBy('platform')
            ->map(fn($perms) => $perms->groupBy('module_name'));
        return view('roles.modals.content', compact('role', 'platformPermissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        checkAuthPermission(PermissionEnum::ROLE_CREATE);
        $role = Role::create($request->validated());
        $role->syncPermissions($request->permissions);
        return redirect()->route('roles.index')->with('success', 'Role has been created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        checkAuthPermission(PermissionEnum::ROLE_EDIT);
        $title = 'Edit Role';
        $permissionNames = $role->permissions()
            ->where('guard_name', 'web')
            ->pluck('name')->toArray();
        $validPermissionNames = array_column(PermissionEnum::cases(), 'value');
        $platformPermissions = Permission::query()
            ->where('guard_name', 'web')
            ->whereIn('name', $validPermissionNames)
            ->get()
            ->groupBy('platform')
            ->map(fn($perms) => $perms->groupBy('module_name'));

        $isSuperAdmin = $role->name === RoleEnum::SUPER_ADMIN->value;
        return view('roles.edit', compact('title', 'role', 'platformPermissions', 'permissionNames', 'isSuperAdmin'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Role $role)
    {
        checkAuthPermission(PermissionEnum::ROLE_EDIT);
        if ($role->guard_name === 'web') {
            $role->update([
                'name' => $role->is_changeable ? $request->name : $role->name,
            ]);
        }
        $role->syncPermissions($request->permissions);
        return redirect()->route('roles.index')->with('success', 'Role has been updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        checkAuthPermission(PermissionEnum::ROLE_DELETE);
        if (!$role->is_changeable || $role->guard_name !== 'web') {
            return redirect()->route('roles.index')->with('error', 'You are not authorized to delete this role.');
        }
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role has been deleted successfully.');
    }
}
