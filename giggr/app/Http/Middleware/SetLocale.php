<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next): mixed
    {
        $locale = $request->segment(1) === 'en' ? 'en' : config('app.locale');
        App::setLocale($locale);
        return $next($request);
    }
}
