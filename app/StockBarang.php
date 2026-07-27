<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
 
class StockBarang extends Model
{
    protected $table = 'stock_barang';
    protected $fillable = ['id','material_code','material_name','specification','type','unit','stock_barang','min_stock','storage_location','unit_price','image','created_by','updated_by'];

    public function totalBarangMasuk() 
    {
     return $this->hasOne('App\BarangMasuk', 'material_code', 'material_code');
    }
    public function totalBarangKeluar() 
    {
     return $this->hasOne('App\BarangKeluar', 'material_code', 'material_code');
    }

}
