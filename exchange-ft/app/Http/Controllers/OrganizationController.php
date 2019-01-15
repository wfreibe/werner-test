<?php
/**
 * Created by PhpStorm.
 * User: wfreiberger
 * Date: 06.11.17
 * Time: 16:15
 */

namespace App\Http\Controllers;

use App\Organization;
use App\User_;
use App\Users_orgs;

class OrganizationController extends Controller {

    /**
     * @deprecated in future lets use EX-ID mapping table
     * @param $email
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserOrganizationsByEmail($email){
        
        $user_ = User_::where('emailAddress', $email)->get();
        $userId = null;
        foreach ($user_ as $user) {
            $userId = $user->userId;
        }

        $users_orgs = Users_orgs::where('userId', $userId)->get();

        $aUsers_org = array();
        foreach ($users_orgs as $users_org) {
            $users_org = $users_org->organizationId;
            array_push($aUsers_org, $users_org);
        }

        $organizations = Organization::find($aUsers_org);

        return response()->json($organizations);

    }

    /**
     * @deprecated in future lets use EX-ID mapping table
     * @param $email
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFirstUserOrganizationByEmail($email) {

        $user_ = User_::where('emailAddress', $email)->get();
        $users_orgs = Users_orgs::where('userId', $user_[0]["userId"])->get();
        $organizations[] = Organization::find($users_orgs[0]['organizationId']);

        return response()->json($organizations);
    }

    /**
     * @param $email
     * @param $orgId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserOrganizationByEmailAndOrgId($email, $orgId) {

        $user_ = User_::where('emailAddress', $email)->get();
        $userId = null;
        foreach ($user_ as $user) {
            $userId = $user->userId;
        }

        $users_orgs = Users_orgs::where('userId', $userId)->get();
        $aUsers_org = array();
        foreach ($users_orgs as $users_org) {
            $users_org = $users_org->organizationId;
            if($users_org == $orgId) {
                array_push($aUsers_org, $users_org);
            }
        }

        $organizations = Organization::find($aUsers_org);

        return response()->json($organizations);

    }

}