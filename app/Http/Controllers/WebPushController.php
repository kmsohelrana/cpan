<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebPushController extends Controller
{
    /**
     * Register the web push token for the authenticated user.
     */
    public function update(Request $request)
    {
        $user = $request->user();
        if ($request->filled('token') && $user) {
            $user = $request->user();
            $user->web_fcm_token = $request->token;
            $user->save();
        }
        return response()->json(['success' => true]);
    }
}
