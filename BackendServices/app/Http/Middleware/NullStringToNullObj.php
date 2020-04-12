<?php

namespace App\Http\Middleware;

use Illuminate\Database\Eloquent\Model;
use Closure;

class NullStringToNullObj
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
        $output = $next($request);
        if($output instanceof Model)
            return response()->json(array_map(function ($value) {
                return $value === "'null'" ? null : $value;
            }, $output->toArray()));

        return $output;
    }
}
