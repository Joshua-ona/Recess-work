<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Stamps the authenticated user's last_active_at column so analytics
 * (daily active users, "online now", usage trend charts) have a real
 * signal to read from. Throttled to one write every 60s per user so we
 * are not hammering the DB on every single request.
 */
class TrackLastActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user) {
            $last = $user->last_active_at;

            if (! $last || $last->diffInSeconds(now()) > 60) {
                $user->timestamps = false; // don't bump updated_at just for this
                $user->forceFill(['last_active_at' => now()])->save();
            }
        }

        return $next($request);
    }
}
