<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;
use App\Karyawan;

class AbsenExport implements FromView
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

        $data = DB::table('absen')
                ->whereDate('date', '>=', $peroideStart)
                ->whereDate('date', '<=', $peroideEnd)
                ->orderBy('date', 'ASC')
                ->groupBy('date')
                ->get();

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
                      ->with(['relAbsenKaryawan' => function($query) use ($peroideStart, $peroideEnd) {
                         $query->whereDate('date', '>=', $peroideStart)->whereDate('date', '<=', $peroideEnd);
                      }]);
                      if(!empty($this->nama)){
                            $result = $master->where('nama', 'LIKE', "%".$this->nama."%");
                      }
                      if (empty($this->nama)) {
                            $result = $master->orderBy('id_karyawan', 'ASC')->get();
                      }else{
                            $result = $master->orderBy('id_karyawan', 'ASC')->get();
                      }
                      $absenKaryawan = $result;
                          
        $totalSakitAll = DB::table('absen')->where('type_hk', 'S')->whereDate('date', '>=', $peroideStart)->whereDate('date', '<=', $peroideEnd)->count();
        $totalIjinAll  = DB::table('absen')->where('type_hk', 'I')->whereDate('date', '>=', $peroideStart)->whereDate('date', '<=', $peroideEnd)->count();
        $totalAlpaAll  = DB::table('absen')->where('type_hk', '0')->whereDate('date', '>=', $peroideStart)->whereDate('date', '<=', $peroideEnd)->count();
        $totalCutiAll  = DB::table('absen')->where('type_hk', 'C')->whereDate('date', '>=', $peroideStart)->whereDate('date', '<=', $peroideEnd)->count();
        $totalKerjAll  = DB::table('absen')->where('type_hk', '1')->whereDate('date', '>=', $peroideStart)->whereDate('date', '<=', $peroideEnd)->count();
        $totalOTAll    = DB::table('absen')->sum('type_ot');

        $dataResult = [
            'data'          => $data,
            'absenKaryawan' => $absenKaryawan,
            'totalSakitAll' => $totalSakitAll,
            'totalIjinAll'  => $totalIjinAll,
            'totalAlpaAll'  => $totalAlpaAll,
            'totalCutiAll'  => $totalCutiAll,
            'totalKerjAll'  => $totalKerjAll,
            'totalOTAll'    => $totalOTAll,
        ];
      
        return view('excel.AbsenExcel', ['data' => $data, 'absenKaryawan' => $absenKaryawan, 'totalSakitAll' => $totalSakitAll, 'totalIjinAll' => $totalIjinAll, 'totalAlpaAll' => $totalAlpaAll, 'totalCutiAll' => $totalCutiAll, 'totalKerjAll' => $totalKerjAll, 'totalOTAll' => $totalOTAll, 'peroideStart' => $peroideStart, 'peroideEnd' => $peroideEnd ]);
    }
}