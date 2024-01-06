<?php

namespace App\Helper;

class ResponseFormatter {

    public static function success($data, $code = 200, $message = null)
    {
        return response()->json([
            'status' => 'OK',
            'code' => $code,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    public static function error($errors, $code = 400, $message = null)
    {
        return response()->json([
            'status' => 'ERR',
            'code' => $code,
            'message' => $message,
            'errors' => $errors
        ], $code);
    }

}