<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
 
class ActivityLog extends Model
{
    protected $table = 'activity_log';
    protected $fillable = ['method','url','ip','user_agent'];

}
