<?php
/**
 * Created by PhpStorm.
 * User: wfreiberger
 * Date: 06.11.17
 * Time: 17:31
 */

namespace App\Http\Controllers;

use App\User_;
use App\Users_orgs;
use App\Groups_orgs;
use App\Users_groups;
use App\Group;
use App\Dlfileentry;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;

use Log;

class DocumentController extends Controller {

    /**
     * @param $email
     * @param $friendlyURL
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserOrganizationProjectDocumentsByEmailAndFriendlyUrl($email, $friendlyURL) {

            $user_ = User_::where('emailAddress', $email)->get();
            $userId = null;
            foreach ($user_ as $user) {
                $userId = $user->userId;
            }

            // TODO check also if the user is in the organization
            // string(12) "53154-122342"
            $groupId = substr( $friendlyURL, strrpos( $friendlyURL, '-' )+1 );
            $dlfileentries = Dlfileentry::where('groupId', $groupId)->get();

            

            /*
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
            }*/

            return response()->json($dlfileentries);

    }

    public function getFistUserOrganizationProjectDocumentsByEmailAndFriendlyUrl($emailFromToken) {

        $user_ = User_::where('emailAddress', $emailFromToken)->get();
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

        $friendlyURL = $aGroupsOfUserId[0]['friendlyURL'];

        // TODO check also if the user is in the organization
        $groupId = substr( $friendlyURL, strrpos( $friendlyURL, '-' )+1 );
        $dlfileentries = Dlfileentry::where('groupId', $groupId)->get();

        return response()->json($dlfileentries);

    }

    /**
     * @param $emailFromToken
     * @param $friendlyURL
     * @param $fileentryId
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function getUserOrganizationProjectDocumentDownloadByEmailAndFriendlyUrlAndFileentryId($emailFromToken, $friendlyURL, $fileentryId) {

        $user_ = User_::where('emailAddress', $emailFromToken)->get();
        $userId = null;
        foreach ($user_ as $user) {
            $userId = $user->userId;
        }

        $groupId = substr( $friendlyURL, strrpos( $friendlyURL, '-' )+1 );
        $dlfileentries = Dlfileentry::where('groupId', $groupId)->get();

        foreach ($dlfileentries as $file) {
            if($file->fileEntryId == $fileentryId) {
                $sCompanyId = $file->companyId;
                $sExtension = $file->extension;
                $sMimeType = $file->mimeType;
                $sTitle = $file->title;
                $sGroupId = $file->groupId;
                $sName = $file->name;

            }
        }
        
        // http://www.sibenye.com/2017/02/16/how-to-integrate-flysystem-with-lumen-framework/
        // https://flysystem.thephpleague.com/docs/adapter/aws-s3/ +++
        // https://nicksilvestro.net/2016/05/28/adding-laravels-storage-facade-into-lumen/ +++
        // https://chrisblackwell.me/upload-files-to-aws-s3-using-laravel/ +++

        $client = S3Client::factory([
            'credentials' => [
                'key'    => getenv('S3_ACCESS_KEY'),
                'secret' => getenv('S3_ACCESS_SECRET'),
            ],
            'region' => 'us-east-1', // us-east-1 // eu-west-1
            'version' => 'latest'
        ]);


        $adapter = new AwsS3Adapter($client, 'nem-exchange-build');
        $filesystem = new Filesystem($adapter);

        // $filesystem->read('10153/10179/2101');
        // $contents = $filesystem->listContents('10153/10179/2101');

        // Retrieve a read-stream
        $path = $sCompanyId."/".$sGroupId."/".$sName."/1.0";
        // $stream = $filesystem->readStream('10153/10179/2101/1.0'); // 23010
        $stream = $filesystem->readStream($path); // 23010
        $contents = stream_get_contents($stream); // stream_get_contents — Reads remainder of a stream into a string
        fclose($stream);

        // http://mattallan.org/posts/getting-started-with-php-streams/ +++
        // https://stackoverflow.com/questions/6914912/streaming-a-large-file-using-php

        //var_dump($sMimeType);
        //var_dump($sTitle);
        //var_dump($sExtension);
        //var_dump($contents);
        //die;

        // send the headers
        header("Content-Disposition: attachment; filename=$sTitle;");
        header("Content-Type: $sMimeType");
        header('Content-Length: ' . 1024 * 1024);
        header('Content-Transfer-Encoding: binary');
        //header('Content-Length: ' . filesize($contents));

        echo $contents;

        //fpassthru($stream);
        //exit;

    }
}