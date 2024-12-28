<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ActivityTimeout
{
    // Durasi timeout dalam menit
    protected $timeout = 30;

    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $lastActivity = session('lastActivityTime');
            if ($lastActivity && (time() - $lastActivity > $this->timeout * 60)) {
                Auth::logout();
                session()->flush();
                return redirect()->route('login')->with('message', 'Session expired due to inactivity.');
            }
            session(['lastActivityTime' => time()]);
        }

        return $next($request);
    }
}