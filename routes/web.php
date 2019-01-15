<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});


$router->group(['prefix' => 'api/v1','namespace' => 'App\Http\Controllers'], function () use ($router) {

    /*
    $router->get('users', function () {
        $res = new App\Http\Controllers\User_Controller();
        $res->index();
        return $res->index();
    });*/

    $router->get('users', ['middleware' => 'auth', function () {
        $res = new App\Http\Controllers\User_Controller();
        return $res->index();
    }]);

    $router->post('users', ['middleware' => 'auth', function (Illuminate\Http\Request $request) {
        $res = new App\Http\Controllers\User_Controller();
        return $res->createUser_($request);
    }]);

    $router->put('users/{userId}', ['middleware' => 'auth', function ($userId, Illuminate\Http\Request $request) {
        $res = new App\Http\Controllers\User_Controller();
        return $res->updateUser_($userId, $request);
    }]);

    $router->get('users/email/{email}', ['middleware' => 'auth', function ($email) {
        $res = new App\Http\Controllers\User_Controller();
        return $res->getUser_ByEmail($email);
    }]);

    $router->get('users/{email}/check', ['middleware' => 'auth', function ($email) {
        $res = new App\Http\Controllers\User_Controller();
        if(empty($res->getUser_ByEmail($email)->original[0]["emailAddress"])) {
            return "false";
        } else {
            return "true";
        }
    }]);

    $router->get('organizations/{orgId}/users', ['middleware' => 'auth', function ($orgId) {
        $res = new App\Http\Controllers\User_Controller();
        if($orgId == "first") {
            $emailFromToken = \App\Http\Controllers\Auth0Controller::getUserEmailFromToken();
            $userController = new \App\Http\Controllers\User_Controller();

            if($userController->checkIfUserExistsByEmail($emailFromToken)) {
                return $res->getFirstOrganizationUsersByEmail($emailFromToken);
            } else {
                return json_encode(array());
            }
        } else {
            return $res->getOrganizationUsersByOrgId($orgId);
        }
    }]);

    /*
    $router->get('organizations/first/users/{userId}', ['middleware' => 'auth', function ($userId) {
        $res = new App\Http\Controllers\User_Controller();
        return $res->getFirstOrganizationUsers($userId);
    }]);
    */
    /*
    $router->get('organizations/first/users/email/{email}', ['middleware' => 'auth', function ($email) {
        $res = new App\Http\Controllers\User_Controller();
        return $res->getFirstOrganizationUsersByEmail($email);
    }]);
    */
    /*
    $router->get('organizations/first/users', ['middleware' => 'auth', function () {
        $emailFromToken = \App\Http\Controllers\Auth0Controller::getUserEmailFromToken();
        $res = new App\Http\Controllers\User_Controller();
        return $res->getFirstOrganizationUsersByEmail($emailFromToken);
    }]);
    */

    $router->get('users/{userId}', ['middleware' => 'auth', function ($userId) {
        $res = new App\Http\Controllers\User_Controller();
        return $res->getUser_ByUserId($userId)->original[0];
    }]);

    $router->delete('users/{userId}', ['middleware' => 'auth', function ($userId) {
        $res = new App\Http\Controllers\User_Controller();
        return $res->deleteUser_($userId);
    }]);

    $router->get('users/search/{searchString}', ['middleware' => 'auth', function ($searchString) {
        $res = new App\Http\Controllers\User_Controller();
        return $res->getUser_BySearchString($searchString);
    }]);



    $router->get('users/{email}/organizations', ['middleware' => 'auth', function ($email) {
        $res = new App\Http\Controllers\OrganizationController();
        return $res->getUserOrganizationsByEmail($email);
    }]);
    $router->get('user/organizations', ['middleware' => 'auth', function () {
        $emailFromToken = \App\Http\Controllers\Auth0Controller::getUserEmailFromToken();
        $userController = new \App\Http\Controllers\User_Controller();
        $res = new App\Http\Controllers\OrganizationController();

        if($userController->checkIfUserExistsByEmail($emailFromToken)) {
            return $res->getUserOrganizationsByEmail($emailFromToken);
        } else {
            return json_encode(array());
        }
    }]);


    
    $router->get('users/{email}/organizations/{orgId}', ['middleware' => 'auth', function ($email, $orgId) {
        $res = new App\Http\Controllers\OrganizationController();
        if($orgId == "first") {
            return $res->getFirstUserOrganizationByEmail($email);
        } else {
            return $res->getUserOrganizationByEmailAndOrgId($email, $orgId);
        }
    }]);
    $router->get('user/organizations/{orgId}', ['middleware' => 'auth', function ($orgId) {
        $emailFromToken = \App\Http\Controllers\Auth0Controller::getUserEmailFromToken();
        $userController = new \App\Http\Controllers\User_Controller();
        $res = new App\Http\Controllers\OrganizationController();

        if($userController->checkIfUserExistsByEmail($emailFromToken)) {
            if($orgId == "first") {
                return $res->getFirstUserOrganizationByEmail($emailFromToken);
            } else {
                return $res->getUserOrganizationByEmailAndOrgId($emailFromToken, $orgId);
            }
        } else {
            return json_encode(array());
        }
    }]);



    $router->get('user/{email}/organizations/{treePath}/projects', ['middleware' => 'auth', function ($email, $treePath) {
        $res = new App\Http\Controllers\GroupController();
        if($treePath == "first") {
            return $res->getFistUserOrganizationProjectsByEmailAndFriendlyUrl($email);
        } else {
            return $res->getUserOrganizationProjectsByEmailAndFriendlyUrl($email, $treePath);
        }
    }]);
    $router->get('user/organizations/{treePath}/projects', ['middleware' => 'auth', function ($treePath) {
        $emailFromToken = \App\Http\Controllers\Auth0Controller::getUserEmailFromToken();
        $userController = new \App\Http\Controllers\User_Controller();
        $res = new App\Http\Controllers\GroupController();

        if($userController->checkIfUserExistsByEmail($emailFromToken)) {
            if($treePath == "first") {
                return $res->getFistUserOrganizationProjectsByEmailAndFriendlyUrl($emailFromToken);
            } else {
                return $res->getUserOrganizationProjectsByEmailAndFriendlyUrl($emailFromToken, $treePath);
            }
        } else {
            return json_encode(array());
        }
    }]);



    $router->get('user/organization/projects/{friendlyURL}/documents', ['middleware' => 'auth', function ($friendlyURL) {
        $emailFromToken = \App\Http\Controllers\Auth0Controller::getUserEmailFromToken();
        $userController = new \App\Http\Controllers\User_Controller();
        $res = new App\Http\Controllers\DocumentController();

        if($userController->checkIfUserExistsByEmail($emailFromToken)) {
            if($friendlyURL == "first") {
                return $res->getFistUserOrganizationProjectDocumentsByEmailAndFriendlyUrl($emailFromToken);
            } else {
                return $res->getUserOrganizationProjectDocumentsByEmailAndFriendlyUrl($emailFromToken, $friendlyURL);
            }
        } else {
            return json_encode(array());
        }
    }]);

    $router->get('user/organization/projects/{friendlyURL}/documents/{fileentryId}', ['middleware' => 'auth', function ($friendlyURL, $fileentryId) {
        $emailFromToken = \App\Http\Controllers\Auth0Controller::getUserEmailFromToken();
        $userController = new \App\Http\Controllers\User_Controller();
        $res = new App\Http\Controllers\DocumentController();

        if($userController->checkIfUserExistsByEmail($emailFromToken)) {
            return $res->getUserOrganizationProjectDocumentDownloadByEmailAndFriendlyUrlAndFileentryId($emailFromToken, $friendlyURL, $fileentryId);
        } else {
            return json_encode(array());
        }
    }]);

});

// for auth see: https://lumen.laravel.com/docs/5.5/middleware
// tutorial for oauth0: https://code.tutsplus.com/tutorials/how-to-secure-a-rest-api-with-lumen--cms-27442