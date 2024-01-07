<?php

namespace App\Http\Middleware;

use App\Helper\ResponseFormatter;
use App\Helper\Utils;
use Closure;
use Illuminate\Support\Str;

class EnsureValidTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $authHeader = $request->header('Authorization');
        if (!$authHeader) {
            return ResponseFormatter::error(null, 401, 'Unauthorized');
        }

        $token = Utils::bearer_token($authHeader);
        if (!$token) {
            return ResponseFormatter::error(null, 401, 'Invalid Token');
        }

        if ($token == env('API_TOKEN')) {
            return $next($request);
        }

        return ResponseFormatter::error(null, 401, 'Unauthorized');
    }
}
