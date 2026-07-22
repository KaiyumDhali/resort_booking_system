<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Visitor;

class TrackVisitor
{
    public function handle($request, Closure $next)
    {
        Visitor::create([
            'ip_address' => $request->ip(),
            'url' => $request->url(),
        ]);

        return $next($request);
    }
}
