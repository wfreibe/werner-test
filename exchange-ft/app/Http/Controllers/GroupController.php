<?php
/**
 * Created by PhpStorm.
 * User: wfreiberger
 * Date: 06.11.17
 * Time: 17:31
 */

namespace App\Http\Controllers;

use App\Organization;
use App\User_;
use App\Users_orgs;
use App\Groups_orgs;
use App\Users_groups;
use App\Group;
use Log;

class GroupController extends Controller {

    /**
     * @deprecated
     * @param $email
     * @param $orgname
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserOrganizationProjectsByEmailAndOrgName($email, $orgname) {

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

        $organizationId = null;
        foreach ($organizations as $organization) {

            $name = $organization->name;
            if ($name == $orgname) {
                $organizationId = $organization->organizationId;
            }
        }

        $groups_orgs = Groups_orgs::where('organizationId', $organizationId)->get();
        $aGroups_org = array();
        foreach ($groups_orgs as $groups_org) {
            $groups_org = $groups_org->groupId;
            array_push($aGroups_org, $groups_org);
        }

        $groups = Group::find($aGroups_org);

        // add projects from user_groups table
        $users_groups = Users_groups::where('userId', $userId)->get();
        $aUsersGroups = array();
        foreach ($users_groups as $users_group) {
            array_push($aUsersGroups, $users_group->groupId);
        }

        $aGroupsOfUserId = array();
        foreach ($groups as $group) {
            if($group->creatorUserId == $userId) {
                array_push($aGroupsOfUserId, $group);
            }
        }
        return response()->json($aGroupsOfUserId);
    }

    /**
     * @param $email
     * @param $frdlurl
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserOrganizationProjectsByEmailAndFriendlyUrl($email, $frdlurl) {

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

            $organizationId = null;
            foreach ($organizations as $organization) {
                $treePath = $organization->treePath;
                if ($treePath == "/".$frdlurl."/") {
                    $organizationId = $organization->organizationId;
                }
            }

            $groups_orgs = Groups_orgs::where('organizationId', $organizationId)->get();

            $aGroups_org = array();
            foreach ($groups_orgs as $groups_org) {
                $groups_org = $groups_org->groupId;
                array_push($aGroups_org, $groups_org);
            }

            $groups = Group::find($aGroups_org);

            // add projects from user_groups table
            $users_groups = Users_groups::where('userId', $userId)->get();
            $aUsersGroups = array();
            foreach ($users_groups as $users_group) {
                array_push($aUsersGroups, $users_group->groupId);
            }

            $aGroupsOfUserId = array();
            foreach ($groups as $group) {
                if($group->creatorUserId == $userId || in_array($group->groupId, $aUsersGroups)) {
                    array_push($aGroupsOfUserId, $group);
                }
            }

            return response()->json($aGroupsOfUserId);

    }


    /**
     * @param $email
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFistUserOrganizationProjectsByEmailAndFriendlyUrl($email) {

        $user_ = User_::where('emailAddress', $email)->get();
        $userId = null;
        foreach ($user_ as $user) {
            $userId = $user->userId;
        }
        //  $user_[0]["userId"]
        $users_orgs = Users_orgs::where('userId', $userId)->get();
        $groups_orgs = Groups_orgs::where('organizationId', $users_orgs[0]['organizationId'])->get();
        $aGroups_org = array();
        foreach ($groups_orgs as $groups_org) {
            $groups_org = $groups_org->groupId;
            array_push($aGroups_org, $groups_org);
        }

        $groups = Group::find($aGroups_org);
        //Log::info('GroupController res: '.print_r($groups, true));

        // add projects from user_groups table
        $users_groups = Users_groups::where('userId', $userId)->get();
        $aUsersGroups = array();
        foreach ($users_groups as $users_group) {
            array_push($aUsersGroups, $users_group->groupId);
        }

        $aGroupsOfUserId = array();
        foreach ($groups as $group) {
            if($group->creatorUserId == $userId) {
                array_push($aGroupsOfUserId, $group);
            }
        }
        return response()->json($aGroupsOfUserId);
    }

}