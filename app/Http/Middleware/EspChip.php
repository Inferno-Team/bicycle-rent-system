<?php

namespace App\Http\Middleware;

use App\Models\Esp32Chip;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EspChip
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->type == 'esp32')
                return $next($request);
            else redirect()->route('login');
        } else response()->json([
            'message' => 'not esp32'
        ], 500);
    }
}
