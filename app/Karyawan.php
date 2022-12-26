<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Absen;
 
class Karyawan extends Model
{
    protected $table = 'karyawan';
    protected $fillable = ['id_karyawan','nama'];

    public function relAbsenKaryawan() 
    {
     return $this->hasMany('App\Absen', 'id_karyawan', 'id_karyawan')->orderBy('date', 'asc');
    }

    public function totalKerja() 
    {
     return $this->hasOne('App\Absen', 'id_karyawan', 'id_karyawan')->where('type_hk', '1');
    }
    public function totalAlpa() 
    {
     return $this->hasOne('App\Absen', 'id_karyawan', 'id_karyawan')->where('type_hk', '0');
    }
    public function totalSakit() 
    {
     return $this->hasOne('App\Absen', 'id_karyawan', 'id_karyawan')->where('type_hk', 'S');
    }
    public function totalIjin() 
    {
     return $this->hasOne('App\Absen', 'id_karyawan', 'id_karyawan')->where('type_hk', 'I');
    }
    public function totalCuti() 
    {
     return $this->hasOne('App\Absen', 'id_karyawan', 'id_karyawan')->where('type_hk', 'C');
    }
    public function totalOt() 
    {
        return $this->hasOne('App\Absen', 'id_karyawan', 'id_karyawan');
    }
}
