<?php
/**
 * Created by PhpStorm.
 * User: wfreiberger
 * Date: 18.01.18
 * Time: 15:37
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class Counter extends Model {

    protected $table = 'counter';
    public $timestamps = false;
    public $primaryKey  = 'name';

}