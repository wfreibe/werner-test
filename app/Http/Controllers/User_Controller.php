<?php
/**
 * Created by PhpStorm.
 * User: wfreiberger
 * Date: 30.10.17
 * Time: 14:38
 */

namespace App\Http\Controllers;

use App\User_;
use App\Users_orgs;
use App\Counter;
use Illuminate\Http\Request;
use Log;

class User_Controller extends Controller {

    // https://medium.com/@paulredmond/how-to-submit-json-post-requests-to-lumen-666257fe8280 +++
    // https://laravel.com/docs/5.5/eloquent +++
    // https://laravel.com/api/5.5/Illuminate/Http/Request.html +++

    const COUNTER_NAME = "com.liferay.counter.model.Counter";

    public function createUser_(Request $request){

        $user_  = array();

        // Retrieve the user by the attributes, or create it if it doesn't exist...
        // $user_ = User_::firstOrCreate(array('name' => 'John'));
        $aRequest = $request->json()->all();
        // Log::info('User_Controller res: '.print_r($aRequest, true));

       if(User_::find($aRequest["emailAddress"]) == NULL) {

           $aRequest["uuid_"] = $this->makeUuid($this->generateUId());

           $counter = Counter::where('name', self::COUNTER_NAME)->get();
           foreach ($counter as $item) {
               $item->name = self::COUNTER_NAME;
               $newValue = intval($item->currentId)+1;
               $item->currentId = $newValue;
               $item->save();
               $aRequest["userId"] = $newValue;
           }

           try {

               unset($aRequest['rating']);
               $user_ = User_::firstOrCreate($aRequest);
               $user_["SUCCESS"] = "true";
               // Log::info('User_Controller res: SUCCESS');

           } catch (\Exception $e) {

               $user_["SUCCESS"] = "false";
               $user_["ERROR-MSG"] = $e;
               //Log::info('User_Controller res: ERROR'.print_r($e, true));
           }

       } else {
           $user_["SUCCESS"] = "false";
           $user_["ERROR-MSG"] = "user is already existing";
       }

        return response()->json($user_);
    }

    public function updateUser_($id, Request $request) {

        // Log::info('User_Controller res: '.print_r($id, true));

        $user_  = User_::find($id);
        $aRequest = $request->json()->all();

        $user_->screenName = $aRequest["screenName"];
        $user_->emailAddress = $aRequest["emailAddress"];
        $user_->greeting = $aRequest["greeting"];
        $user_->firstName = $aRequest["firstName"];
        $user_->lastName = $aRequest["lastName"];
        $user_->jobTitle = $aRequest["jobTitle"];

        $user_->save();
        return response()->json($user_);
    }

    public function deleteUser_($id) {

        // Log::info('User_Controller res: '.print_r($id, true));

        $user_  = User_::find($id);
        $user_->delete();
        return response()->json('Removed successfully.');
    }

    public function index(){
        $user_  = User_::all();
        return response()->json($user_);
    }

    public function getUser_ByEmail($email){
        $user_ = User_::where('emailAddress', $email)->get();
        return response()->json($user_);
    }

    public function checkIfUserExistsByEmail($email) {
        $user_ = User_::where('emailAddress', $email)->get();
        if(count($user_)>0) {
            return true;
        } else {
            return false;
        }
    }

    public function getUser_ByUserId($userId){
        $intUserId = intval($userId);
        $user_ = User_::where('userId', $intUserId)->get();
        return response()->json($user_);
    }

    public function getUser_BySearchString($searchString) {
        $user_ = User_::where('lastName','LIKE',"%{$searchString}%")->orWhere('firstName','LIKE',"%{$searchString}%")->orWhere('emailAddress','LIKE',"%{$searchString}%")->get();
        return response()->json($user_);
    }

    public function getOrganizationUsersByOrgId($orgId) {
        $intOrgId = intval($orgId);
        $user_orgs = Users_orgs::where('organizationId', $intOrgId)->get();

        $aUsersInOrg = array();
        foreach ($user_orgs as $user_org) {
            $users_org = $user_org->userId;
            array_push($aUsersInOrg, $users_org);
        }

        $users = User_::find($aUsersInOrg);
        return response()->json($users);

    }

    public function getFirstOrganizationUsers($userId) {

        $intUserId = intval($userId);
        $users_orgs = Users_orgs::where('userId', $intUserId)->get();
        $users_orgs = Users_orgs::where('organizationId', $users_orgs[0]['organizationId'])->get();

        $aUsersInOrg = array();
        foreach ($users_orgs as $user_org) {
            $users_org = $user_org->userId;
            array_push($aUsersInOrg, $users_org);
        }
        $users = User_::find($aUsersInOrg);

        return response()->json($users);

    }

    /**
     * @deprecated in future lets use EX-ID mapping table
     * @param $email
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFirstOrganizationUsersByEmail($email) {

        $user_ = User_::where('emailAddress', $email)->get();
        $users_orgs = Users_orgs::where('userId', $user_[0]["userId"])->get();
        $users_orgs = Users_orgs::where('organizationId', $users_orgs[0]['organizationId'])->get();

        $aUsersInOrg = array();
        foreach ($users_orgs as $user_org) {
            $users_org = $user_org->userId;
            array_push($aUsersInOrg, $users_org);
        }
        $users = User_::find($aUsersInOrg);

        return response()->json($users);

    }

    /**
     * Returns generated unique ID.
     *
     * @return string
     */
    private function generateUId() {
        return substr( md5( uniqid( '', true ).'|'.microtime() ), 0, 32 );
    }
    /**
     * @param $id
     * @return mixed
     */
    private function makeUuid($id) {
        $id = substr_replace($id, "-", 8, 0);
        $id = substr_replace($id, "-", 13, 0);
        $id = substr_replace($id, "-", 18, 0);
        $id = substr_replace($id, "-", 23, 0);
        return $id;
    }

}
