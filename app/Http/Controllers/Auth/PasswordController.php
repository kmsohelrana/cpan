<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Rules\PasswordRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{

    /**
     * Show the form for changing the user's password.
     */
    public function index()
    {
        $title = 'Change Password';
        return view('auth.change-password', compact('title'));
    }

    /**
     * Update the user's password.
     */
    public function update(Request $request)
    {
        $validated = $request->validateWithBag('updatePassword', [
            'password' => ['required', 'current_password'],
            'new_password' => ['required', 'string', new PasswordRule(), 'confirmed'],
        ]);

        $validated['new_password'] = Hash::make($validated['new_password']);

        $request->user()->update([
            'password' => $validated['new_password'],
            'password_history' => $validated['new_password'],
            'password_changed_at' => now()->toDateTimeString(),
            'change_password' => false,
        ]);

        Auth::logout();
        return redirect()->route('login');
    }
}
