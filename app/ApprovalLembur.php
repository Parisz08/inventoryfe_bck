<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
 
class ApprovalLembur extends Model
{
    protected $table = 'approval_lembur';
    protected $fillable = ['code_spl','id_karyawan','nama','posisi','unit','hari','tanggal','actual_jam','uraian_pekerjaan','status_approval','approved_by','approved_date','alasan_approved','approved_to','approved_to_id','created_at','created_by','updated_at','updated_by'];

}
