<?php

namespace App\Helper;

use Illuminate\Support\Str;

class Utils {

    public static function public_path($path = '')
    {
        return env('PUBLIC_PATH', base_path('public')) . ($path ? '/' . $path : $path);
    }

    public static function bearer_token($token)
    {
        if (Str::startsWith($token, 'Bearer ')) {
            return Str::substr($token, 7);
        }
        return null;
    }

}