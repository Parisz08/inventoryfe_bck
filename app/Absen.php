<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
 
class Absen extends Model
{
    protected $table = 'absen';
    protected $fillable = ['id_karyawan','nama','date','type_hk','type_ot','created_by','updated_by'];

}
