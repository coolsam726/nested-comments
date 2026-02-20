<?php

namespace Hadialharbi\NestedComments\Http\Middleware;

use Closure;
use Hadialharbi\NestedComments\NestedComments;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GuestCommentatorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        app(NestedComments::class)->setOrGetGuestId();
        app(NestedComments::class)->setOrGetGuestName();

        return $next($request);
    }
}
