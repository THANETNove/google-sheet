<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;


use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateUserStatusReport
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (Auth::check()) {
         

            // หากสถานะของผู้ใช้คือ 0 ให้ผ่าน
            return $next($request);
        }

        // หากไม่ใช่สถานะ 0
        return response('Unauthorized: Invalid status', 403);
    }
}