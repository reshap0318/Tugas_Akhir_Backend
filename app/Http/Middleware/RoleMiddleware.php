<?php

namespace App\Http\Middleware;

use Closure;

class RoleMiddleware
{

    public function handle($request, Closure $next, $guard = null)
    {
        $response = $next($request);
        $myrole= $request->user()->role;
        if($myrole==$guard){
            return $response;
        }else{
          return response()->json([
            'success' => false,
            'description' => 'Kamu Tidak Memiliki Akses Kesini'
          ],403);
        }
    }
}
