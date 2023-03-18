<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
 
class SettingApproval extends Model
{
    protected $table = 'setting_approval';
    protected $fillable = ['nama_requester','id_karyawan_requester','nama_approver','id_karyawan_approver','created_by','updated_by'];

}
