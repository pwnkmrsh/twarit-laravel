<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class CheckBanned
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth()->check() && auth()->user()->usertype === 'banned') {
            auth()->logout();

            $request->session()->invalidate();

            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json([
                    'errors' => trans('v3.your_account_banned'),
                ]);
            }

            Session::flash('error.message', trans('v3.your_account_banned'));
            return redirect()->route('home');
        }

        return $next($request);
    }
}
