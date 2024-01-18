<?php

namespace App\Http\Middleware;

use Closure;

class XSS
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $input = $request->all();
        array_walk_recursive($input,function(&$input){
            ##remove all tag in every input if it had any
            ##then put it back to $request
            $input=strip_tags($input);

        });

        $request->merge($input);
        return $next($request);
    }
}
