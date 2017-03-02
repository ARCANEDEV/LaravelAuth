<?php namespace Arcanedev\LaravelAuth\Http\Middleware;

use Arcanedev\LaravelAuth\Services\UserImpersonator;
use Closure;
use Illuminate\Support\Facades\Auth;

/**
 * Class     Impersonate
 *
 * @package  Arcanesoft\Auth\Http\Middleware
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class Impersonate
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
        if (UserImpersonator::isEnabled() && UserImpersonator::isImpersonating()) {
            Auth::guard($guard)->onceUsingId(UserImpersonator::getUserId());
        }

        return $next($request);
    }
}
