<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFeeClearance
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->hasRole('student')) {
            $student = $user->student;
            if (!$student || !$student->isFeeCleared()) {
                // If the request is for the payment blocked page itself, allow it
                if ($request->routeIs('student.payment.blocked')) {
                    return $next($request);
                }
                
                return redirect()->route('student.payment.blocked');
            }
        }

        return $next($request);
    }
}
