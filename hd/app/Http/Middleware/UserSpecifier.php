<?php

namespace App\Http\Middleware;

use Closure;

class UserSpecifier
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$users)
    {
         $usertype = auth()->user()->role;

        if(!in_array($usertype,$users))
        {
            return redirect()->back();
        }
        return $next($request);
    }
}
