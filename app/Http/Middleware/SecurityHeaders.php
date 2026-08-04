<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Добавляет базовые заголовки безопасности ко всем ответам.
 *
 * CSP и HSTS намеренно не выставляются: обе политики требуют предварительной
 * проверки на реальном трафике (CSP ломает инлайновые скрипты админки,
 * HSTS фиксирует HTTPS на длительный срок).
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        return $response;
    }
}
