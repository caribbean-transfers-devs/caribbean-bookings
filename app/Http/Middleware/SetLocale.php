<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $languages = ['es'];
        $locale = config('app.locale');
        if( isset($request->route()->parameters()['locale'])){
            if(in_array($request->route()->parameters()['locale'], $languages) ){
                $locale = $request->route()->parameters()['locale'];
            }else{
                abort(404);            
            }
        }
        
        App::setLocale($locale);
        return $next($request);
    }
}
