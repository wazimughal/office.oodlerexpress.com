<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriberGaurd
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
        $userData=session()->get('userData'); 
        
        if( Auth::user() && Auth::user()->group_id==config('constants.groups.subscriber')){
         return $next($request);
        }
         else{
            abort(403, sprintf('You are not allowed to Access the Page. Please login'));
             
         }
    }
}
 