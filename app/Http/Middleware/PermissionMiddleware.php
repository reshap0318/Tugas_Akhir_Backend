<?php

namespace App\Http\Middleware;

use Closure;

class PermissionMiddleware
{

    public function handle($request, Closure $next)
    {
        $permission = $this->getNamedRoute($request);

        $akses = $request->user()->role->permissions->where('name',$permission)->isNotEmpty();
        if($akses || empty($permission)){
          return $next($request);
        }else{
          return response()->json([
            'success' => false,
            'description' => 'Kamu Tidak Memiliki Akses Kesini'
          ],403);
        }
    }

    protected function getNamedRoute($request)
    {
        $routes = $request->route();
        $name = "";

        foreach ($routes as $route) {
            ( isset($route['as']) ) ? $name = $route['as'] : "";
        }
        return $name;
    }
}
