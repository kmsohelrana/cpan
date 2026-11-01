<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@sslwireless.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt(12345678),
                'status' => 1,
            ]
        );
        $superAdmin->syncRoles(RoleEnum::SUPER_ADMIN->value);

        $admin = User::firstOrCreate(
            ['email' => 'admin@sslwireless.com'],
            [
                'name' => 'System Admin',
                'password' => bcrypt(12345678),
                'status' => 1,
            ]
        );
        $admin->syncRoles(RoleEnum::ADMIN->value);
    }
}
