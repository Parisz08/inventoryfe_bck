<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
 
class BarangMasuk extends Model
{
    protected $table = 'barang_masuk';
    protected $fillable = ['id','material_code','qty','note','date','created_by','updated_by'];

}
