<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
 
class Payroll extends Model
{
    protected $table = 'payroll';
    protected $fillable = ['id_karyawan','periode_start','periode_end','piutang','pinjaman','created_by','updated_by'];

}
