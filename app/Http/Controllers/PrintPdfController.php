<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Carbon\Carbon;
use App\Karyawan;

class PrintPdfController extends Controller
{
    
    public function printSlipGaji($travel_latter_no)
    {
        $monthPeriode = strtoupper(\Carbon\Carbon::parse($peroideStart)->formatLocalized('%B %Y'));

        $data =  Karyawan::withCount(['totalKerja' => function($query) use ($peroideStart, $peroideEnd, $idKaryawan) {
                         $query->whereDate('date', '>=', $peroideStart)->whereDate('date', '<=', $peroideEnd)->where('id_karyawan', $idKaryawan);
                      },
                      'totalAlpa' => function($query) use ($peroideStart, $peroideEnd, $idKaryawan) {
                         $query->whereDate('date', '>=', $peroideStart)->whereDate('date', '<=', $peroideEnd)->where('id_karyawan', $idKaryawan);
                      },
                      'totalSakit' => function($query) use ($peroideStart, $peroideEnd, $idKaryawan) {
                         $query->whereDate('date', '>=', $peroideStart)->whereDate('date', '<=', $peroideEnd)->where('id_karyawan', $idKaryawan);
                      },
                      'totalIjin' => function($query) use ($peroideStart, $peroideEnd, $idKaryawan) {
                         $query->whereDate('date', '>=', $peroideStart)->whereDate('date', '<=', $peroideEnd)->where('id_karyawan', $idKaryawan);
                      },
                      'totalCuti' => function($query) use ($peroideStart, $peroideEnd, $idKaryawan) {
                         $query->whereDate('date', '>=', $peroideStart)->whereDate('date', '<=', $peroideEnd)->where('id_karyawan', $idKaryawan);
                      },
                      'totalOt' => function($query) use ($peroideStart, $peroideEnd, $idKaryawan) {
                         $query->select(DB::raw('SUM(type_ot)'))->whereDate('date', '>=', $peroideStart)->whereDate('date', '<=', $peroideEnd)->where('id_karyawan', $idKaryawan);
                      }])
                      ->with(['relPayroll' => function($query) use ($peroideStart, $peroideEnd, $idKaryawan) {
                         $query->where('periode_start', '>=', $peroideStart)->where('periode_end', '<=', $peroideEnd)->where('id_karyawan', $idKaryawan);
                      }])->where('id_karyawan', $idKaryawan)->first();

            $relPay = json_decode($data, true);
            // $data   = array('data' => $masterFix, 'relPay' =>$relPay['rel_payroll'], 'periode' => $monthPeriode);

        return view('pdf.slipGaji', compact('data', 'countWeight'));
    }

    public function printSpl(Request $request)
    {
         $data = DB::table('approval_lembur')
                ->where('code_spl', $request->code_spl)
                ->orderBy('created_at', 'DESC')
                ->get();

        return view('pdf.printSpl', compact('data'));
    }

   public function printEhp(Request $request)
   {
      // ====================================  EHP  ============================
        $peroideStart = $request->periode_start;
        $peroideEnd   = $request->periode_end;

         $master =  Karyawan::withCount(['totalKerja' => function($query) use ($peroideStart, $peroideEnd) {
                         $query->whereDate('date', '>=', $peroideStart)->whereDate('date', '<=', $peroideEnd);
                      },
                      'totalOt' => function($query) use ($peroideStart, $peroideEnd) {
                         $query->select(DB::raw('SUM(type_ot)'))->whereDate('date', '>=', $peroideStart)->whereDate('date', '<=', $peroideEnd);
                      }]);
                      // ->select('nama','unit','id_karyawan')
                      if(!empty($request->input('nama'))){
                            $result = $master->where('nama', 'LIKE', "%".$request->nama."%");
                      }
                      if(!empty($request->input('jabatan'))){
                            $result = $master->where('jabatan', 'LIKE', "%".$request->jabatan."%");
                      }
                      if(!empty($request->input('unit'))){
                            $result = $master->where('unit', 'LIKE', "%".$request->unit."%");
                      }

                      if (empty($request->input('nama')) && empty($request->input('jabatan')) && empty($request->input('unit'))  ) {
                            $result = $master->orderBy('total_kerja_count', 'desc')->orderBy('total_ot_count', 'desc')->get();
                      }else{
                            $result = $master->orderBy('total_kerja_count', 'desc')->orderBy('total_ot_count', 'desc')->get();
                      }
         $data = $result;

     return view('pdf.printEhp', compact('data', 'peroideStart', 'peroideEnd'));
   }

}
