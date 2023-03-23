<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $roles): Response
    {
        $roles = explode('|', $roles);
        // dd($roles);
        $flag = false;
        foreach ($roles as $role) {
            if ($role == auth()->user()->role->name) {
                $flag = true;
                return $next($request);
            }
        }
        if (!$flag) {
            return response()->json(['not access']);
        }
        // if (auth()->user()->roles->name == "Admin" || auth()->user()->roles->name == "Owner" || auth()->user()->roles->name == "Manager") {
        //     return $next($request);
        // } else {
        //     return response()->json(['not access']);
        // }
    }
}
