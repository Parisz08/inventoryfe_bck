<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;
use App\Karyawan;

class PayrollBankExport implements FromView
{
    public function __construct($request)
    {
        $this->peroideStart = $request->periode_start;
        $this->peroideEnd   = $request->periode_end;
        $this->nama_bank    = $request->nama_bank;
    }

    public function view(): View
    {
        $peroideStart = $this->peroideStart;
        $peroideEnd   = $this->peroideEnd;
        $nama_bank    = $this->nama_bank;

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

                      if($nama_bank == 'BANK BSI')
                      {
                        $result = $master->where('bank', 'Bank Syariah Indonesia')->orderBy('id_karyawan', 'ASC')->get();
                      }else if($nama_bank == 'BANK MANDIRI')
                      {
                        $result = $master->where('bank', 'Bank Mandiri')->orderBy('id_karyawan', 'ASC')->get();
                      }else
                      {
                        $result = $master->whereNotIn('bank', ['Bank Syariah Indonesia','Bank Mandiri'])->orderBy('id_karyawan', 'ASC')->get();
                      }
                      
                     $data = $result;

                     // generate total gaji
                     $totalG = [];
                     foreach ($data as $i => $row) {

                        $total = ((($row->harian == 0) ? (($row->bulanan / $row->relPayroll['periode_total_hk']) * $row->total_kerja_count) : ($row->harian * $row->total_kerja_count))
                                    + $row->tj_jabatan_skill + $row->transport + $row->makan + $row->total_ot_count * (($row->unit == 'Head Quarter') ? 250000 : 22619)
                                    - ( $row->relPayroll['piutang'] + $row->relPayroll['pinjaman'] + $row->jht + $row->jkm + $row->jkk + $row->jp + $row->jks ));

                        array_push($totalG, round($total, 0));
                     }
                     $totalGaji = array_sum($totalG);

      
        return view('excel.PayrollBankExcel', ['data' => $data, 'totalGaji' => $totalGaji, 'nama_bank' => $nama_bank]);
    }
}