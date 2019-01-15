<?php
/**
 * Created by PhpStorm.
 * User: wfreiberger
 * Date: 30.10.17
 * Time: 14:36
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class User_ extends Model {

    // comment disable updated_at and created_at
    public $timestamps = false;
    protected $table = 'user_';

    public $primaryKey  = 'userId';

    protected $fillable = ['uuid_', 'userId', 'emailAddress', 'firstName', 'lastName', 'greeting', 'jobTitle', 'screenName'];

    // rawUser.userId,
    // rawUser.contactId,
    // rawUser.screenName,
    // rawUser.emailAddress,
    // rawUser.languageId,
    // rawUser.greeting,
    // rawUser.firstName,
    // rawUser.lastName,
    // rawUser.jobTitle,
    // rawUser.rating,
    // rawUser.portraitId

}
