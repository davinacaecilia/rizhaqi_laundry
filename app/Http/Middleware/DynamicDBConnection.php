<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class DynamicDBConnection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public function handle(Request $request, Closure $next): Response
    {

        if (Auth::check()) {

            $role = Auth::user()->role;

            switch ($role) {
                case 'admin':
                case 'owner':
                    Config::set('database.default', 'mysql');
                    break;
                case 'pegawai':
                    Config::set('database.default', 'pegawai_mysql');
                    break;
                case 'user':
                    Config::set('database.default', 'user_mysql');
                    break;
                default:
                    Config::set('database.default', 'mysql');
                    break;
            }

            DB::reconnect();
        }

        return $next($request);
    }
}
