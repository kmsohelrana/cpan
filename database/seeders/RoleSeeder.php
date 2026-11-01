<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $superAdmin = Role::firstOrCreate(
            ['name' => RoleEnum::SUPER_ADMIN->value, 'guard_name' => 'web'],
            ['is_changeable' => 0]
        );

        $superAdmin->givePermissionTo(Permission::query()->where('guard_name', 'web')->get());

        $admin = Role::firstOrCreate(
            ['name' => RoleEnum::ADMIN->value, 'guard_name' => 'web'],
            ['is_changeable' => 0]
        );

        $admin->givePermissionTo(Permission::query()->where('guard_name', 'web')->get());
    }
}
