<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
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

        // Роуты группы объявлены с префиксом {lang}/admin, поэтому route() и redirect()->route()
        // требуют параметр lang. Без дефолта вызов без него бросает UrlGenerationException, а вызов
        // с позиционным аргументом (route('users.show', $id)) молча подставляет id вместо языка.
        URL::defaults(['lang' => $locale]);

        $request->route()->forgetParameter('lang');

        return $next($request);
    }
}
