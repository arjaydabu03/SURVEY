<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;
use Auth;
class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            return route("login");
        }
    }
    public function handle($request, Closure $next, ...$guards)
    {
        if ($sanctum = $request->cookie("survey_coockie")) {
            $request->headers->set("Authorization", "Bearer " . $sanctum);
        }
        $this->authenticate($request, $guards);

        return $next($request);
    }
}
