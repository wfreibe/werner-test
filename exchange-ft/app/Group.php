<?php
/**
 * Created by PhpStorm.
 * User: wfreiberger
 * Date: 07.11.17
 * Time: 11:08
 */

namespace App;

use Illuminate\Database\Eloquent\Model;


class Group extends Model{

    protected $table = 'group_';
    protected $primaryKey = 'groupId';
    protected $fillable = ['groupId', 'name', 'friendlyURL'];

}