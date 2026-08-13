<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;

class BarangKeluarExport implements FromView
{
    public function __construct($request)
    {
        $this->no_sj = $request->no_sj;
        $this->material_code = $request->material_code;
        $this->material_name = $request->material_name;
        $this->type          = $request->type;
        $this->unit          = $request->unit;
        $this->description   = $request->description;
        $this->divisi        = $request->divisi;
        $this->date          = $request->date;
    }

    public function view(): View
    {
        $master = DB::table('barang_keluar')
                    ->leftJoin('stock_barang', 'barang_keluar.material_code', '=', 'stock_barang.material_code')
                    ->select('barang_keluar.id','barang_keluar.material_code','material_name','specification','type','unit','qty','description','divisi','no_sj','date','diserahkan','disetujui','diterima','barang_keluar.created_by','barang_keluar.created_at');
        if(!empty($this->no_sj)){
            $result = $master->where('no_sj', $this->no_sj);
        }
        if(!empty($this->material_code)){
            $result = $master->where('material_code', $this->material_code);
        }
        if(!empty($this->material_name)){
            $result = $master->where('material_name', 'LIKE', "%".$this->material_name."%");
        }
        if(!empty($this->type)){
            $result = $master->where('type', $this->type);
        }
        if(!empty($this->unit)){
            $result = $master->where('unit', $this->unit);
        }
        if(!empty($this->description)){
            $result = $master->where('description', 'LIKE', "%".$this->description."%");
        }
        if(!empty($this->divisi)){
            $result = $master->where('divisi', $this->divisi);
        }
        if(!empty($this->date)){
            $date      = $this->date;
            $dateStart = date(substr($date, 0, 10));
            $dateEnd   = date(substr($date, -10));

            $result = $master->whereDate('date', '>=', $dateStart);
            $result = $master->whereDate('date', '<=', $dateEnd);
        }
        

        if (empty($this->material_code) && empty($this->material_name) && empty($this->type) && empty($this->unit) && empty($this->divisi) && empty($this->date) ) {
            $result = $master->orderBy('barang_keluar.created_at', 'DESC')->get();
        }else{
            $result = $master->orderBy('barang_keluar.created_at', 'DESC')->get();
        }

        $data  = $result;
      
        return view('excel.BarangKeluarExcel', ['data' => $data]);
    }
}