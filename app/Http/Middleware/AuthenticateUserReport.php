<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;



class AuthenticateUserReport
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $username = $request->query('username');
        $password = $request->query('password');


        // ค้นหาผู้ใช้ในฐานข้อมูล
        $user = DB::table('users')->where('name', $username)->where('status', 0)->first();

        if ($user && password_verify($password, $user->password)) {
            // หากพบผู้ใช้และรหัสผ่านถูกต้อง ให้ผ่านไปยัง Controller

            $request->merge([
                'user_id' => $user->id,
                'username' => $username,
                'password' => $password
            ]);

            session(['company_id' => $user->id]);
            session(['company_status' => 0]);
            session(['company_name' =>   $user->company]);
            return $next($request);
        }

        // หากตรวจสอบไม่ผ่าน ส่งกลับ Unauthorized
        // error 401
        return response()->view('errors.unauthorized', [], 401);
    }
}
