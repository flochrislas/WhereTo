<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tells the search engines not to index the response (X-Robots-Tag header).
 * For the HTML fragments the page loads by AJAX (results, details): they are pieces of a page,
 * not pages, and the standalone place pages carry the same content with a title, a canonical URL and links.
 * See doc/10-seo.md.
 */
class NoIndex
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $response->headers->set('X-Robots-Tag', 'noindex');

        return $response;
    }
}
