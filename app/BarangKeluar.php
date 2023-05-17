<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
 
class BarangKeluar extends Model
{
    protected $table = 'barang_keluar';
    protected $fillable = ['id','material_code','qty','description','divisi','no_sj','diserahkan','disetujui','diterima','date','created_by','updated_by'];

}
