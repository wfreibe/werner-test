<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Controllers\Auth0Controller;
use Log;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {}

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot() {

        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('api', function ($request) {

            //$header = $request->header('authorization');
            $header = $request->header('Authorization');

            try {

                $auth0Controller = new Auth0Controller();
                $res = $auth0Controller->setCurrentToken($header);
                return $res;

            }
            catch(\Auth0\SDK\Exception\CoreException $e) {

                header('HTTP/1.0 401 Unauthorized');
                echo $e;
                exit();
            }

            /*
            if($header && $header == "birds fly south") {
                return new User();
            }*/

            return null;

        });
    }
}
