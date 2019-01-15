<?php
/**
 * Created by PhpStorm.
 * User: wfreiberger
 * Date: 10.01.18
 * Time: 15:34
 */

namespace App\Http\Middleware;

use Closure;
use Log;

class CorsMiddleware {

    // https://www.codementor.io/chiemelachinedum/steps-to-enable-cors-on-a-lumen-api-backend-e5a0s1ecx

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $headers = [
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Methods'     => 'POST, GET, OPTIONS, PUT, DELETE',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Max-Age'           => '86400',
            'Access-Control-Allow-Headers'     => 'Content-Type, Authorization, X-Requested-With'
        ];

        if ($request->isMethod('OPTIONS'))
        {
            return response()->json('{"method":"OPTIONS"}', 200, $headers);
        }

        $response = $next($request);
        foreach($headers as $key => $value)
        {
            $response->header($key, $value);
        }

        // Log::info('CorsMiddleware res: '.print_r($response, true));

        return $response;
    }

}




