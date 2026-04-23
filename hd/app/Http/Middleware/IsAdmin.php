<?php

namespace App\Http\Middleware;

use Closure;

class IsAdmin
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
        if(auth()->check()) {
            if(auth()->user()->role == 'admin') {
                return $next($request);
            } else if(auth()->user()->role = 'user') {
                return redirect('/');
            } else {
                return redirect('/');
            }
        }
        else {
            return redirect('/login')->with('error' , 'Please login first');
        }
    }
}
