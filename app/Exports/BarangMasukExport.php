<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;

class BarangMasukExport implements FromView
{
    public function __construct($request)
    {
        $this->material_code = $request->material_code;
        $this->material_name = $request->material_name;
        $this->type          = $request->type;
        $this->note          = $request->note;
        $this->date          = $request->date;
    }

    public function view(): View
    {
        $master = DB::table('barang_masuk')
                    ->leftJoin('stock_barang', 'barang_masuk.material_code', '=', 'stock_barang.material_code')
                    ->select('barang_masuk.id','barang_masuk.material_code','material_name','specification','type','qty','note','date','barang_masuk.created_by','barang_masuk.created_at');
        if(!empty($this->material_code)){
            $result = $master->where('material_code', $this->material_code);
        }
        if(!empty($this->material_name)){
            $result = $master->where('material_name', 'LIKE', "%".$this->material_name."%");
        }
        if(!empty($this->type)){
            $result = $master->where('type', $this->type);
        }
        if(!empty($this->note)){
            $result = $master->where('note', 'LIKE', "%".$this->note."%");
        }
        if(!empty($this->date)){
            $date      = $this->date;
            $dateStart = date(substr($date, 0, 10));
            $dateEnd   = date(substr($date, -10));

            $result = $master->whereDate('date', '>=', $dateStart);
            $result = $master->whereDate('date', '<=', $dateEnd);
        }
        

        if (empty($this->material_code) && empty($this->material_name) && empty($this->type) && empty($this->note) && empty($this->date) ) {
            $result = $master->orderBy('barang_masuk.created_at', 'DESC')->get();
        }else{
            $result = $master->orderBy('barang_masuk.created_at', 'DESC')->get();
        }

        $data  = $result;
      
        return view('excel.BarangMasukExcel', ['data' => $data]);
    }
}