<?php

namespace App\Http\Middleware;

use Closure;

class CanPiracy
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (\Auth::check()) {
            if (! \Auth::user()->piracy) {
                \Auth::logout();

                return redirect('/');
            }
        } else {
            \Auth::logout();

            return redirect('/');
        }

        return $next($request);
    }
}
