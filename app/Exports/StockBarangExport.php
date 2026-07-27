<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;
use App\StockBarang;

class StockBarangExport implements FromView
{
    public function __construct($request)
    {
        $this->material_code    = $request->material_code;
        $this->material_name    = $request->material_name;
        $this->type             = $request->type;
        $this->unit             = $request->unit;
        $this->storage_location = $request->storage_location;
        $this->date             = $request->date;
    }

    public function view(): View
    {
        $master = StockBarang::withCount(['totalBarangMasuk' => function($query) {
                        $query->select(DB::raw('SUM(qty)'));
                    },
                    'totalBarangKeluar' => function($query) {
                        $query->select(DB::raw('SUM(qty)'));
                    }]);
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
            $result = $master->where('unit', 'LIKE', "%".$this->unit."%");
        }
        if(!empty($this->storage_location)){
            $result = $master->where('storage_location', 'LIKE', "%".$this->storage_location."%");
        }
        if(!empty($this->date)){
            $date      = $this->date;
            $dateStart = date(substr($date, 0, 10));
            $dateEnd   = date(substr($date, -10));

            $result = $master->whereDate('date', '>=', $dateStart);
            $result = $master->whereDate('date', '<=', $dateEnd);
        }
        

        if (empty($this->material_code) && empty($this->material_name) && empty($this->type) && empty($this->unit) && empty($this->storage_location) && empty($this->date) ) {
            $result = $master->orderBy('material_name', 'ASC')->get();
        }else{
            $result = $master->orderBy('material_name', 'ASC')->get();
        }

        $data  = $result;
      
        return view('excel.StockBarangExcel', ['data' => $data]);
    }
}