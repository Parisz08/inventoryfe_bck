<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;
use App\Karyawan;

class KaryawanExport implements FromView
{
    public function __construct($request)
    {
        $this->id_karyawan = $request->id_karyawan;
        $this->nama        = $request->nama;
        $this->nik         = $request->nik;
        $this->jabatan     = $request->jabatan;
        $this->unit        = $request->unit;
        $this->status      = $request->status;
    }

    public function view(): View
    {
        $master = DB::table('karyawan');
        if(!empty($this->id_karyawan)){
            $result = $master->where('id_karyawan', 'LIKE', "%".$this->id_karyawan."%");
        }
        if(!empty($this->nama)){
            $result = $master->where('nama', 'LIKE', "%".$this->nama."%");
        }
        if(!empty($this->nik)){
            $result = $master->where('nik', 'LIKE', "%".$this->nik."%");
        }
        if(!empty($this->jabatan)){
            $result = $master->where('jabatan', 'LIKE', "%".$this->jabatan."%");
        }
        if(!empty($this->unit)){
            $result = $master->where('unit', 'LIKE', "%".$this->unit."%");
        }
        if(!empty($this->status)){
            $result = $master->where('status', 'LIKE', "%".$this->status."%");
        }
        
        if (empty($this->id_karyawan) && empty($this->nama) && empty($this->nik) && empty($this->jabatan)  && empty($this->unit)  && empty($this->status)) {
            $result = $master->orderBy('id_karyawan', 'ASC')->get();
        }else{
            $result = $master->orderBy('id_karyawan', 'ASC')->get();
        }

        $data  = $result;
      
        return view('excel.KaryawanExcel', ['data' => $data]);
    }
}