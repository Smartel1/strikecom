<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Validator;

class DefineLocale
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
        $locale = request()->route('locale');

        $available = 'ru,en,es,all';

        Validator::validate(
            ['locale' => $locale],
            ['locale' => 'in:'.$available]);

        app()->instance('locale', $locale);

        return $next($request);
    }
}
