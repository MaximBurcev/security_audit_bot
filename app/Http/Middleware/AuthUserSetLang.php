<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthUserSetLang
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $arUrl = array_diff(explode('/', $request->getPathInfo()), ['']);
        $locale = array_shift($arUrl);

        app()->setLocale($locale);

        $request->route()->forgetParameter('lang');

        return $next($request);
    }
}
