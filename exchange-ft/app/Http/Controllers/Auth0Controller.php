<?php
/**
 * Created by PhpStorm.
 * User: wfreiberger
 * Date: 10.11.17
 * Time: 13:00
 */

namespace App\Http\Controllers;

use Auth0\SDK\JWTVerifier;
use Auth0\SDK\Helpers\Cache\FileSystemCacheHandler;
use Log;

class Auth0Controller extends Controller {

    public static $_userEmailFromToken;

    /**
     * @param $token
     * @return object
     * @throws \Auth0\SDK\Exception\CoreException
     */
    public function setCurrentToken($token) {

        try {

            $verifier = new JWTVerifier([
                'valid_audiences' => explode(",", getenv('VALID_AUDIENCES')),
                'authorized_iss' => explode(",", getenv('AUTHORIZED_ISS')),
                'client_secret' => getenv('AUTH0_CLIENT_SECRET'),
                'supported_algs' => explode(",", getenv('SUPPORTED_ALGS')),
                'cache' => new FileSystemCacheHandler() // This parameter is optional. By default no cache is used to fetch the Json Web Keys.
            ]);

            $decoded = $verifier->verifyAndDecode($token);

            $aDecoded =  (array) $decoded;
            if(getenv('USE_TESTUSER_EMAIL')=="TRUE") {
                $aDecoded[getenv('ACCESS_TOKEN_USER_EMAIL')] = getenv('ACCESS_TOKEN_USER_EMAIL_TESTUSER');
            }
            if (array_key_exists(getenv('ACCESS_TOKEN_USER_EMAIL'), $aDecoded)) {
                Auth0Controller::$_userEmailFromToken = $aDecoded[getenv('ACCESS_TOKEN_USER_EMAIL')];
                Log::info(__CLASS__. 'user email from token: '.$aDecoded[getenv('ACCESS_TOKEN_USER_EMAIL')]);
            }

            return $decoded;

        }
        catch(\Auth0\SDK\Exception\CoreException $e) {
            throw $e;
        }

    }

    /**
     * @return mixed
     */
    public static function getUserEmailFromToken() {
        return Auth0Controller::$_userEmailFromToken;
    }


}