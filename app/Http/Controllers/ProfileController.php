<?php

namespace App\Http\Controllers;

use App\Helpers\FileHelper;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        $title = 'Profile';
        return view('profile.index', compact('title'));
    }

    public function edit()
    {
        $title = 'Edit Profile';
        $user = authUser();
        return view('profile.edit', compact('title', 'user'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,JPG,JPEG,PNG', 'max:1280'],
        ]);

        $user = authUser();
        $user->name = $request->name;

        if ($request->hasFile('image')) {
            if ($user->image) {
                FileHelper::deleteFile($user->getRawOriginal('image'));
            }
            $user->image = FileHelper::uploadFile('users', $request->image);
        }

        $user->save();

        return redirect()->route('profile.index')->with('success', 'Profile updated successfully');
    }
}
