<?php

namespace App\Http\Middleware;

use Closure;
use GuzzleHttp\Promise\Is;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (isAdmin()) {
            return $next($request);
        }
        else{
            return redirect(route('admin.login'));
        }
    }
}
