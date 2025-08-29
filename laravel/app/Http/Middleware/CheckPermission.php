<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class CheckPermission
{
public function handle(Request $request, Closure $next, string $permission): Response
{
    $user = $request->user();

    // Allow super-admin (id = 1) to bypass permission checks
    if ($user && $user->id === 1) {
        return $next($request);
    }

    // Normal permission check
    if (! $user || ! $user->hasPermission($permission)) {
        abort(403, 'Unauthorized');
    }

    return $next($request);
}
}
