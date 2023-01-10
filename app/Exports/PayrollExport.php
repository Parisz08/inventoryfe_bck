<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;
use App\Karyawan;

class PayrollExport implements FromView
{
    public function __construct($request)
    {
        $this->peroideStart = $request->periode_start;
        $this->peroideEnd   = $request->periode_end;
        $this->nama         = $request->nama;
    }

    public function view(): View
    {
        $peroideStart = $this->peroideStart;
        $peroideEnd   = $this->peroideEnd;

        $master =  Karyawan::withCount(['totalKerja' => function($query) use ($peroideStart, $peroideEnd) {
                         $query->whereDate('date', '>=', $peroideStart)->whereDate('date', '<=', $peroideEnd);
                      },
                      'totalAlpa' => function($query) use ($peroideStart, $peroideEnd) {
                         $query->whereDate('date', '>=', $peroideStart)->whereDate('date', '<=', $peroideEnd);
                      },
                      'totalSakit' => function($query) use ($peroideStart, $peroideEnd) {
                         $query->whereDate('date', '>=', $peroideStart)->whereDate('date', '<=', $peroideEnd);
                      },
                      'totalIjin' => function($query) use ($peroideStart, $peroideEnd) {
                         $query->whereDate('date', '>=', $peroideStart)->whereDate('date', '<=', $peroideEnd);
                      },
                      'totalCuti' => function($query) use ($peroideStart, $peroideEnd) {
                         $query->whereDate('date', '>=', $peroideStart)->whereDate('date', '<=', $peroideEnd);
                      },
                      'totalOt' => function($query) use ($peroideStart, $peroideEnd) {
                         $query->select(DB::raw('SUM(type_ot)'))->whereDate('date', '>=', $peroideStart)->whereDate('date', '<=', $peroideEnd);
                      }])
                      ->with(['relPayroll' => function($query) use ($peroideStart, $peroideEnd) {
                         $query->where('periode_start', '>=', $peroideStart)->where('periode_end', '<=', $peroideEnd);
                      }]);
                      if(!empty($this->nama)){
                            $result = $master->where('nama', 'LIKE', "%".$this->nama."%");
                      }

                      if (empty($this->nama)) {
                            $result = $master->orderBy('id_karyawan', 'ASC')->get();
                      }else{
                            $result = $master->orderBy('id_karyawan', 'ASC')->get();
                      }

        $data  = $result;
      
        return view('excel.PayrollExcel', ['data' => $data, 'peroideStart' => $peroideStart, 'peroideEnd' => $peroideEnd ]);
    }
}