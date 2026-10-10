<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        if (! empty($roles)) {
            $userRole = strtolower(trim((string) $request->user()->role));
            $normalizedRoles = array_map(fn ($r) => strtolower(trim((string) $r)), $roles);

            if (! in_array($userRole, $normalizedRoles, true)) {
                abort(403, 'Access denied: You do not have permission to access this module.');
            }
        }

        return $next($request);
    }
}
