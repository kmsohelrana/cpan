<?php

use App\Enums\PermissionEnum;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->seed(\Database\Seeders\PermissionsSeeder::class);
});

test('roles create page can be rendered by authorized user', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(PermissionEnum::ROLE_CREATE->value);

    $this->actingAs($user)
        ->get(route('roles.create'))
        ->assertOk()
        ->assertSee('Create Role');
});

test('roles edit page can be rendered by authorized user', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(PermissionEnum::ROLE_EDIT->value);

    $role = Role::create(['name' => 'Test Role', 'guard_name' => 'web']);

    $this->actingAs($user)
        ->get(route('roles.edit', $role))
        ->assertOk()
        ->assertSee('Edit Role');
});

test('roles show modal content can be rendered by authorized user', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(PermissionEnum::ROLE_VIEW->value);

    $role = Role::create(['name' => 'Test View Role', 'guard_name' => 'web']);
    $role->givePermissionTo(PermissionEnum::ROLE_LIST->value);

    $this->actingAs($user)
        ->get(route('roles.show', $role))
        ->assertOk();
});

test('roles list page can be rendered by authorized user', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(PermissionEnum::ROLE_LIST->value);

    $this->actingAs($user)
        ->get(route('roles.index'))
        ->assertOk();
});

test('role can be created without parent role', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(PermissionEnum::ROLE_CREATE->value);

    $response = $this->actingAs($user)
        ->post(route('roles.store'), [
            'name' => 'Support Agent',
            'permissions' => [PermissionEnum::USER_VIEW->value],
        ]);

    $response->assertRedirect(route('roles.index'));
    expect(Role::where('name', 'Support Agent')->exists())->toBeTrue();
});

test('role can be updated without parent role', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(PermissionEnum::ROLE_EDIT->value);

    $role = Role::create(['name' => 'Old Name', 'guard_name' => 'web']);

    $response = $this->actingAs($user)
        ->put(route('roles.update', $role), [
            'name' => 'New Name',
            'permissions' => [PermissionEnum::ROLE_VIEW->value],
        ]);

    $response->assertRedirect(route('roles.index'));
    expect($role->fresh()->name)->toBe('New Name');
});

