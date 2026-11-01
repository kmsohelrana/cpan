<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class FileHelper
{
    public static function uploadFile($path, $file)
    {
        return Storage::disk('public')->put($path, $file);
    }

    public static function uploadFileUnderCurrentDate($path, $file)
    {
        return Storage::disk('public')->put($path . '/' . now()->toDateString(), $file);
    }

    public static function deleteFile($path)
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
