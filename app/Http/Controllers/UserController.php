<?php

namespace App\Http\Controllers;

use App\Enums\CommonEnum;
use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusEnum;
use App\Helpers\FileHelper;
use App\Helpers\LogHelper;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Requests\User\StoreRequest;
use App\Jobs\SendEmailJob;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        checkAuthPermission(PermissionEnum::USER_LIST);
        $title = 'Users';
        $statuses = StatusEnum::dropdown();
        $data = $this->userList($request);
        return view('users.index', compact('title', 'data', 'statuses'));
    }

    /**
     * Display the data listing of the resource.
     */
    public function userList(Request $request)
    {
        if (!userHasPermission(PermissionEnum::USER_LIST)) {
            return webResponse(Response::HTTP_UNAUTHORIZED, 'You do not have permission to view users.');
        }

        $title = 'Users';

        $users = User::query()->with(['roles'])
            ->when(
                $request->filled('name'),
                fn($query) => $query->where('name', 'like', '%' . $request->name . '%')
            )->when(
                $request->filled('email'),
                fn($query) => $query->where('email', 'like', '%' . $request->email . '%')
            )->when(
                $request->filled('mobile'),
                fn($query) => $query->where('mobile', 'like', '%' . $request->mobile . '%')
            )->when(
                $request->filled('employee_id'),
                fn($query) => $query->where('employee_id', 'like', '%' . $request->employee_id . '%')
            )->when(
                $request->filled('status'),
                fn($query) => $query->where('status', $request->status)
            )->when(
                $request->filled('role'),
                fn($query) => $query->whereHas('roles', fn($query) => $query->where('name', $request->role))
            )->orderBy('id', 'desc')->paginate(CommonEnum::PER_PAGE->value());

        return view('users.table', compact('title', 'users'));
    }

    /**
     * Display the specified resource.
     */
    public function show($user)
    {
        if (!userHasPermission(PermissionEnum::USER_VIEW)) {
            return webResponse(Response::HTTP_UNAUTHORIZED, 'You do not have permission to view user.');
        }

        $user = User::with(['roles'])->findOrFail($user);

        return view('users.modals.content', compact('user'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        try {
            if (!userHasPermission(PermissionEnum::USER_CREATE)) {
                return webResponse(Response::HTTP_UNAUTHORIZED, 'You do not have permission to create user.');
            }
            $validated = $request->validated();

            $plainPassword = app()->isLocal() ? '12345678' : rand(10000000, 99999999);
            $validated['password'] = hashPassword($plainPassword);

            if ($request->hasFile('image')) {
                $validated['image'] = FileHelper::uploadFile('users', $request->image);
            }
            $user = User::create($validated);
            $user->syncRoles($validated['role']);

            if (app()->isProduction()) {
                if (isEmailConfigured() && !empty($user->email)) {
                    $emailBody = view('emails.user.credentials', [
                        'name'     => $user->name,
                        'email'    => $user->email,
                        'password' => $plainPassword,
                        'loginUrl' => url('/login'),
                    ])->render();

                    SendEmailJob::dispatch(
                        to: $user->email,
                        subject: 'Your Account Credentials — ' . config('app.name'),
                        body: $emailBody,
                        isHtml: true,
                    );
                }
            }

            return webResponse(Response::HTTP_OK, 'User has been created successfully.');
        } catch (\Exception $e) {
            return webResponse(Response::HTTP_INTERNAL_SERVER_ERROR, 'Something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource for editing.
     */
    public function edit($user)
    {
        if (!userHasPermission(PermissionEnum::USER_EDIT)) {
            return webResponse(Response::HTTP_UNAUTHORIZED, 'You do not have permission to update user.');
        }

        $user = User::with(['roles'])->findOrFail($user);

        $targetRole = $user->roles->first()?->name;
        if (!isSuperAdmin()) {
            if (isAdmin() && $targetRole === RoleEnum::SUPER_ADMIN->value) {
                return webResponse(Response::HTTP_UNAUTHORIZED, 'You do not have permission to update this user.');
            }
            if (!isAdmin() && in_array($targetRole, [RoleEnum::SUPER_ADMIN->value, RoleEnum::ADMIN->value])) {
                return webResponse(Response::HTTP_UNAUTHORIZED, 'You do not have permission to update this user.');
            }
        }
        return webResponse(Response::HTTP_OK, 'Success', $user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, User $user)
    {
        if (!userHasPermission(PermissionEnum::USER_EDIT)) {
            return webResponse(Response::HTTP_UNAUTHORIZED, 'You do not have permission to update user.');
        }
        $user->load(['roles']);
        $currentRole  = $user->roles->first()?->name;
        $requestedRole = $request->role;
        if (!isSuperAdmin()) {
            if (isAdmin()) {
                if ($currentRole === RoleEnum::SUPER_ADMIN->value || $requestedRole === RoleEnum::SUPER_ADMIN->value) {
                    return webResponse(Response::HTTP_UNAUTHORIZED, 'You do not have permission to perform this action.');
                }
            } else {
                $restricted = [RoleEnum::SUPER_ADMIN->value, RoleEnum::ADMIN->value];
                if (in_array($currentRole, $restricted) || in_array($requestedRole, $restricted)) {
                    return webResponse(Response::HTTP_UNAUTHORIZED, 'You do not have permission to perform this action.');
                }
            }
        }
        $validated = $request->validated();
        if ($request->hasFile('image')) {
            if ($user->image) {
                FileHelper::deleteFile($user->getRawOriginal('image'));
            }
            $validated['image'] = FileHelper::uploadFile('users', $request->image);
        }
        $user->update($validated);
        $user->syncRoles($request->role);
        return webResponse(Response::HTTP_OK, 'User has been updated successfully.');
    }

    /**
     * Update the specified user's password.
     */
    public function updatePassword(User $user)
    {
        if (!userHasPermission(PermissionEnum::USER_PASSWORD_UPDATE)) {
            return webResponse(Response::HTTP_UNAUTHORIZED, 'You do not have permission to update user password.');
        }
        try {
            $plainPassword = app()->isLocal() ? '12345678' : rand(10000000, 99999999);

            $user->update([
                'password'        => hashPassword($plainPassword),
                'change_password' => true,
            ]);

            if (isEmailConfigured() && !empty($user->email)) {
                $emailBody = view('emails.user.credentials', [
                    'name'     => $user->name,
                    'email'    => $user->email,
                    'password' => $plainPassword,
                    'loginUrl' => url('/login'),
                    'type'     => 'reset',
                ])->render();

                SendEmailJob::dispatch(
                    to: $user->email,
                    subject: 'Your Password Has Been Reset — ' . config('app.name'),
                    body: $emailBody,
                    isHtml: true,
                );
            }

            return webResponse(Response::HTTP_OK, 'User password has been reset successfully.');
        } catch (\Exception $e) {
            return webResponse(Response::HTTP_INTERNAL_SERVER_ERROR, 'Something went wrong.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if (!userHasPermission(PermissionEnum::USER_DELETE)) {
            return webResponse(Response::HTTP_FORBIDDEN, 'You do not have permission to delete user.');
        }

        if (app()->environment('production')) {
            return webResponse(Response::HTTP_FORBIDDEN, 'Deleting users is not allowed in production environment.');
        }

        if ($user->image) {
            FileHelper::deleteFile($user->getRawOriginal('image'));
        }
        $user->delete();
        return webResponse(Response::HTTP_OK, 'User has been deleted successfully.');
    }
}
