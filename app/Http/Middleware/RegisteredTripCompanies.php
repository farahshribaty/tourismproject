<?php

namespace App\Http\Middleware;

use App\Models\TripCompany;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RegisteredTripCompanies
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $attraction = TripCompany::where('attraction_admin_id','=',$request->user()->id)->first();

        if(!isset($attraction)){
            return response()->json([
                'success'=> false,
                'message'=> 'You should register your company before doing this operation!',
            ]);
        }
        return $next($request);
    }
}
