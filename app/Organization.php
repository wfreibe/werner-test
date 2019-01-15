<?php
/**
 * Created by PhpStorm.
 * User: wfreiberger
 * Date: 06.11.17
 * Time: 16:11
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model {

    protected $table = 'organization_';
    protected $primaryKey = 'organizationId';
    protected $fillable = ['organizationId', 'name', 'treePath'];

}