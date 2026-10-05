<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserCanAccessTicket
{
    public function handle(Request $request, Closure $next): Response
    {
        $ticket = $request->route('ticket');

        if ($ticket === null) {
            abort(404);
        }

        $user = $request->user();

        if ($user === null) {
            abort(401, 'Authentication required.');
        }

        if (in_array($user->role, ['agent', 'manager'], true)) {
            return $next($request);
        }

        if ((int) $ticket->user_id === (int) $user->id) {
            return $next($request);
        }

        abort(403, 'You do not have permission to access this ticket.');
    }
}
