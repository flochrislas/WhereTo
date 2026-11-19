<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sets the language of the response from the route group: 'locale:ja' in routes/web.php for the /ja/... pages.
 * __() then reads lang/ja.json, and <html lang> prints it. See doc/12-multilingual-ui.md.
 */
class SetLocale
{
    /** $locale: the parameter after the colon in the middleware name ('locale:ja' gives 'ja') */
    public function handle(Request $request, Closure $next, string $locale): Response
    {
        app()->setLocale($locale);

        return $next($request);
    }
}
