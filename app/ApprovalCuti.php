<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
 
class ApprovalCuti extends Model
{
    protected $table = 'approval_cuti';
    protected $fillable = ['id_karyawan','dibuat_oleh','alasan_cuti','dari_tanggal','sampai_tanggal','status_approval','approved_by','approved_date','alasan_approved','approved_to','type','bukti','total_day'];

}
