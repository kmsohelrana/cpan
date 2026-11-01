<?php

if (!function_exists('authUser')) {
    function authUser()
    {
        return auth()->user();
    }
}

if (!function_exists('isSuperAdmin')) {
    function isSuperAdmin()
    {
        return authUser()?->hasRole(\App\Enums\RoleEnum::SUPER_ADMIN->value) ?? false;
    }
}

if (!function_exists('isAdmin')) {
    function isAdmin()
    {
        return authUser()?->hasRole(\App\Enums\RoleEnum::ADMIN->value) ?? false;
    }
}

if (!function_exists('checkAuthPermission')) {
    function checkAuthPermission($permission)
    {
        abort_if(!authUser() || !authUser()->can($permission), 401);
    }
}

if (!function_exists('userHasPermission')) {
    function userHasPermission($permission)
    {
        return authUser()?->can($permission) ?? false;
    }
}

if (!function_exists('webResponse')) {
    function webResponse($code, $message = null, $data = null, $errors = null)
    {
        return response()->json([
            'message' => $message,
            'errors' => $errors,
            'data' => $data,
        ], $code);
    }
}

if (!function_exists('addAttrVal')) {
    function addAttrVal($path, $value, $optional = '')
    {
        return call_user_func_array('Request::is', $path) ? $value : $optional;
    }
}

if (!function_exists('getHierarchyRoles')) {
    function getHierarchyRoles(?\App\Models\Role $exclude = null, $excludeIds = []): \Illuminate\Support\Collection
    {
        $roles = \App\Models\Role::where('guard_name', 'web')->orderBy('name')->get();

        if ($exclude) {
            $roles = $roles->where('id', '!=', $exclude->id)->values();
        }

        if (!empty($excludeIds)) {
            $roles = $roles->whereNotIn('id', $excludeIds)->values();
        }

        return $roles;
    }
}

if (!function_exists('customPagination')) {
    function customPagination($collection)
    {
        return $collection->appends(request()->query())->links();
    }
}

if (!function_exists('isSmsConfigured')) {
    function isSmsConfigured(): bool
    {
        return !empty(config('ismsplus.sid'))
            && !empty(config('ismsplus.base_url'))
            && !empty(config('ismsplus.token'));
    }
}

if (!function_exists('isEmailConfigured')) {
    function isEmailConfigured(): bool
    {
        return !empty(config('mail.mailers.smtp.host'))
            && !empty(config('mail.mailers.smtp.username'))
            && !empty(config('mail.mailers.smtp.password'));
    }
}

if (!function_exists('hashPassword')) {
    function hashPassword(string $password): string
    {
        return \Illuminate\Support\Facades\Hash::make($password);
    }
}

if (!function_exists('verifyPassword')) {
    function verifyPassword(string $password, string $hashedPassword): bool
    {
        return \Illuminate\Support\Facades\Hash::check($password, $hashedPassword);
    }
}

if (!function_exists('globalSetting')) {
    function globalSetting(?string $key = null, mixed $default = null): mixed
    {
        $service = app(\App\Services\GlobalSettingService::class);

        if (is_null($key)) {
            return $service;
        }

        return $service->get($key, $default);
    }
}
