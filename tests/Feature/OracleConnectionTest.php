<?php

use App\Console\Commands\TestOracleConnectionCommand;
use App\Models\OracleModel;
use Illuminate\Support\Facades\DB;

test('multiple oracle connections are configured', function () {
    $connections = config('database.connections');

    expect($connections)->toHaveKey('oracle');
    expect($connections)->toHaveKey('oracle2');
    expect($connections)->toHaveKey('oracle3');

    expect($connections['oracle']['driver'])->toBe('oracle');
    expect($connections['oracle2']['driver'])->toBe('oracle');
    expect($connections['oracle3']['driver'])->toBe('oracle');
});

test('oracle model defaults to oracle connection', function () {
    $model = new class extends OracleModel {
        protected $table = 'DUMMY';
    };

    expect($model->getConnectionName())->toBe('oracle');

    $model2 = new class extends OracleModel {
        protected $connection = 'oracle2';
        protected $table = 'DUMMY';
    };

    expect($model2->getConnectionName())->toBe('oracle2');
});

test('oracle test artisan command rejects invalid connection name', function () {
    $this->artisan('oracle:test', ['connection' => 'non_existent_db'])
        ->expectsOutputToContain('is not defined')
        ->assertExitCode(1);
});

test('destructive database commands can be prohibited', function () {
    DB::prohibitDestructiveCommands(true);

    $this->artisan('db:wipe')
        ->expectsOutputToContain('prohibited from running')
        ->assertExitCode(1);

    DB::prohibitDestructiveCommands(false);
});

