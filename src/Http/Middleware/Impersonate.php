<?php namespace Arcanedev\LaravelAuth\Http\Middleware;

use Arcanedev\LaravelAuth\Services\UserImpersonator;
use Auth;
use Closure;

/**
 * Class     Impersonate
 *
 * @package  Arcanesoft\Auth\Http\Middleware
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class Impersonate
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
        if (UserImpersonator::isEnabled() && UserImpersonator::isImpersonating()) {
            auth()->onceUsingId(UserImpersonator::getUserId());
        }

        return $next($request);
    }
}
