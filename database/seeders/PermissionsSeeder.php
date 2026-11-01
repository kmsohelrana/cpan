<?php

namespace Database\Seeders;

use App\Enums\PermissionEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    public function run()
    {
        $now = now();

        $permissions = [
            // User Permissions
            ['name' => PermissionEnum::USER_LIST->value, 'guard_name' => 'web', 'module_name' => 'User', 'platform' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => PermissionEnum::USER_VIEW->value, 'guard_name' => 'web', 'module_name' => 'User', 'platform' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => PermissionEnum::USER_CREATE->value, 'guard_name' => 'web', 'module_name' => 'User', 'platform' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => PermissionEnum::USER_EDIT->value, 'guard_name' => 'web', 'module_name' => 'User', 'platform' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => PermissionEnum::USER_DELETE->value, 'guard_name' => 'web', 'module_name' => 'User', 'platform' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => PermissionEnum::USER_PASSWORD_UPDATE->value, 'guard_name' => 'web', 'module_name' => 'User', 'platform' => 'web', 'created_at' => $now, 'updated_at' => $now],

            // Role Permissions
            ['name' => PermissionEnum::ROLE_LIST->value, 'guard_name' => 'web', 'module_name' => 'Role', 'platform' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => PermissionEnum::ROLE_VIEW->value, 'guard_name' => 'web', 'module_name' => 'Role', 'platform' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => PermissionEnum::ROLE_CREATE->value, 'guard_name' => 'web', 'module_name' => 'Role', 'platform' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => PermissionEnum::ROLE_EDIT->value, 'guard_name' => 'web', 'module_name' => 'Role', 'platform' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => PermissionEnum::ROLE_DELETE->value, 'guard_name' => 'web', 'module_name' => 'Role', 'platform' => 'web', 'created_at' => $now, 'updated_at' => $now],

            // Global Setting Permissions
            ['name' => PermissionEnum::GLOBAL_SETTING->value, 'guard_name' => 'web', 'module_name' => 'Global Setting', 'platform' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => PermissionEnum::GLOBAL_SETTING_GENERAL->value, 'guard_name' => 'web', 'module_name' => 'Global Setting', 'platform' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => PermissionEnum::GLOBAL_SETTING_SMS_TEMPLATE->value, 'guard_name' => 'web', 'module_name' => 'Global Setting', 'platform' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => PermissionEnum::GLOBAL_SETTING_PUSH_TEMPLATE->value, 'guard_name' => 'web', 'module_name' => 'Global Setting', 'platform' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => PermissionEnum::GLOBAL_SETTING_EMAIL_TEMPLATE->value, 'guard_name' => 'web', 'module_name' => 'Global Setting', 'platform' => 'web', 'created_at' => $now, 'updated_at' => $now],

            // Infrastructure Monitoring
            ['name' => PermissionEnum::HEALTH_MONITORING_VIEW->value, 'guard_name' => 'web', 'module_name' => 'Health Monitoring', 'platform' => 'web', 'created_at' => $now, 'updated_at' => $now],
        ];

        Permission::upsert($permissions, ['name', 'guard_name'], ['module_name', 'platform', 'updated_at']);

        // Delete obsolete permissions no longer defined in PermissionEnum
        $validNames = array_column(PermissionEnum::cases(), 'value');
        Permission::whereNotIn('name', $validNames)->delete();

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
