<?php namespace Arcanedev\LaravelAuth\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

/**
 * Class     TrackLastActivity
 *
 * @package  Arcanedev\LaravelAuth\Http\Middleware
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class TrackLastActivity
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure                  $next
     * @param  string|null               $guard
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        /** @var  \Arcanedev\LaravelAuth\Models\User  $user */
        $user = Auth::guard($guard)->user();

        if ( ! is_null($user) && $this->isEnabled())
            $user->updateLastActivity();

        return $next($request);
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */
    /**
     * Check if the tracking is enabled.
     *
     * @return bool
     */
    protected function isEnabled()
    {
        return config('laravel-auth.track-activity.enabled', false);
    }
}
