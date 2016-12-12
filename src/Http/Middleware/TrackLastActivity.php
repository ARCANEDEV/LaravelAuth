<?php namespace Arcanedev\LaravelAuth\Http\Middleware;

use Closure;

/**
 * Class     TrackLastActivity
 *
 * @package  Arcanedev\LaravelAuth\Http\Middleware
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class TrackLastActivity
{
    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure                  $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /** @var \Arcanedev\LaravelAuth\Models\User $user */
        if ( ! is_null($user = auth()->user()) && $this->isEnabled()) {
            $user->updateLastActivity();
        }

        return $next($request);
    }

    /**
     * Check if the tracking is enabled.
     *
     * @return mixed
     */
    private function isEnabled()
    {
        return config('laravel-auth.track-activity.enabled', false);
    }
}
