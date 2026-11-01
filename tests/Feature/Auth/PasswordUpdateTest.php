<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('password can be updated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/password/change')
        ->put('/password/update', [
            'password' => '12345678',
            'new_password' => 'Secure@P8xQ9#',
            'new_password_confirmation' => 'Secure@P8xQ9#',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('login'));

    $this->assertTrue(Hash::check('Secure@P8xQ9#', $user->refresh()->password));
});

test('correct password must be provided to update password', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/password/change')
        ->put('/password/update', [
            'password' => 'wrong-password',
            'new_password' => 'Secure@Pass123',
            'new_password_confirmation' => 'Secure@Pass123',
        ]);

    $response
        ->assertSessionHasErrorsIn('updatePassword', 'password')
        ->assertRedirect('/password/change');
});
