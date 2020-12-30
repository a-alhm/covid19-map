<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Cases;

class ValidateRequestBody
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        $data = json_decode($request->getContent(), true);
        $rules = [
            'country_code' => 'required|alpha|size:2',
            'new_confirmed' => 'required|integer',
            'total_confirmed' => 'required|integer',
            'new_deaths' => 'required|integer',
            'total_deaths' => 'required|integer',
            'new_recovered' => 'required|integer',
            'total_recovered' => 'required|integer'
        ];

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 400); 
        }

        return $next($request);
    }
}
