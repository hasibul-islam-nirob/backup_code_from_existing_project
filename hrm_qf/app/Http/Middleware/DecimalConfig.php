<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

class DecimalConfig
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
     
        //applicable only for reports routes
        //check if session has decimal Number config
        //if config does not exist => add it

        $current_route_name = Route::getCurrentRoute()->uri();
        if(strpos($current_route_name, '/reports') !== false){
            if(Session::has('decimalConfig') == false){ // need to create config
                $d = intval(
                    DB::table('mfn_config')
                    ->where('title', 'decimalNumberForReport')
                    ->first()
                    ->content
                );
                Session::put('decimalConfig', $d);
            }
        }

        return $next($request);
    }
}
