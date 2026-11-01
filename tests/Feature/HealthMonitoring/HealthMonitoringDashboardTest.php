<?php

use App\Enums\PermissionEnum;
use App\Models\User;
use Spatie\Permission\Models\Permission;

function healthMonitoringUser(): User
{
    Permission::query()->firstOrCreate(
        ['name' => PermissionEnum::HEALTH_MONITORING_VIEW->value, 'guard_name' => 'web'],
        ['module_name' => 'Health Monitoring', 'platform' => 'web']
    );

    $user = User::factory()->create();
    $user->givePermissionTo(PermissionEnum::HEALTH_MONITORING_VIEW->value);

    return $user;
}

test('guests are redirected to login', function () {
    $this->get(route('health-monitoring.index'))->assertRedirect(route('login'));
});

test('users without the permission are denied', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('health-monitoring.index'))
        ->assertStatus(401);
});

test('users with the permission can view the dashboard', function () {
    $user = healthMonitoringUser();

    $this->actingAs($user)
        ->get(route('health-monitoring.index'))
        ->assertOk()
        ->assertSee('Infrastructure Monitoring')
        ->assertSee('Active Sessions')
        ->assertSee('Outbound Connectivity');
});

test('chart-data endpoint rejects an unknown component', function () {
    $user = healthMonitoringUser();

    $this->actingAs($user)
        ->getJson(route('health-monitoring.chart-data', ['component' => 'not-a-real-component']))
        ->assertStatus(404);
});

test('chart-data endpoint returns json for a known component', function () {
    $user = healthMonitoringUser();

    $this->actingAs($user)
        ->getJson(route('health-monitoring.chart-data', ['component' => 'storage']))
        ->assertOk()
        ->assertJsonStructure(['data']);
});

test('api traffic endpoints return the expected shape', function () {
    $user = healthMonitoringUser();

    $this->actingAs($user)
        ->getJson(route('health-monitoring.api-traffic.by-app'))
        ->assertOk()
        ->assertJsonStructure(['data' => ['labels', 'series']]);

    $this->actingAs($user)
        ->getJson(route('health-monitoring.api-traffic.by-route'))
        ->assertOk()
        ->assertJsonStructure(['data' => ['labels', 'series']]);
});

test('outbound connectivity endpoint returns a checked row per integration', function () {
    $user = healthMonitoringUser();

    $response = $this->actingAs($user)
        ->getJson(route('health-monitoring.outbound-connectivity'))
        ->assertOk();

    expect($response->json('data'))->toBeArray()->not->toBeEmpty();
});
