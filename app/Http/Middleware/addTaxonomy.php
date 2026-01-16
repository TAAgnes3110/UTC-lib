<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;

class addTaxonomy
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, $value)
    {
        $request->merge(['taxonomy'=>$value]);
        return $next($request);
    }
}
