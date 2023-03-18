<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Library\Responses;
use Illuminate\Support\Facades\DB;
use validator;
use App\Http\Traits\LoggedUser;
use App\Karyawan;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // ====================================  TOTAL KARYAWAN  ================================
        $totalKaryawan = DB::table('karyawan')->pluck('id_karyawan')->count();

        // ====================================  TOTAL GAJI BULAN INI  ============================
        $day   = date('d');
        $month = date('m');
        $year  = date('Y');
        $lastMont = \Carbon\Carbon::now()->subMonth(+1)->format('Y-m');
        $plusMont = \Carbon\Carbon::now()->subMonth(-1)->format('Y-m');

        if ($day >= 16) {
            $peroideStart = $year.'-'.$month.'-16' ;
            $peroideEnd   = $plusMont.'-15';
        }else if ($day <= 15) {
            $peroideStart = $lastMont.'-16' ;
            $peroideEnd   = $year.'-'.$month.'-15';
        }

        $cek = DB::table('absen')->where('date', $peroideStart)->first();
        if ($cek) {
            $master =  Karyawan::withCount(['totalKerja' => function($query) use ($peroideStart, $peroideEnd) {
                         $query->whereDate('date', '>=', $peroideStart)->whereDate('date', '<=', $peroideEnd);
                          },
                          'totalOt' => function($query) use ($peroideStart, $peroideEnd) {
                             $query->select(DB::raw('SUM(type_ot)'))->whereDate('date', '>=', $peroideStart)->whereDate('date', '<=', $peroideEnd);
                          }])
                          ->with(['relPayroll' => function($query) use ($peroideStart, $peroideEnd) {
                             $query->where('periode_start', '>=', $peroideStart)->where('periode_end', '<=', $peroideEnd);
                          }]);
                          $result = $master->orderBy('id_karyawan', 'ASC')->get();

            $gajiPokok = [];
            $lembur    = [];
            foreach ($result as $i => $value) {
                $totalKerja[$i] = $value->total_kerja_count;
                $totalOT[$i]    = $value->total_ot_count;

                array_push($gajiPokok, (($value->harian == 0) ? (($value->bulanan / $value->relPayroll->periode_total_hk) * $totalKerja[$i]) : ($value->harian * $totalKerja[$i])));
                array_push($lembur, $totalOT[$i] * (($value->unit == 'Head Quarter') ? 250000 : 22619));
            }

            $totalGajiPokok = array_sum($gajiPokok);
            $totalTunjangan = DB::table('karyawan')->sum('tj_jabatan_skill');
            $totalTransport = DB::table('karyawan')->sum('transport');
            $totalMakan     = DB::table('karyawan')->sum('makan');
            $totalLembur    = array_sum($lembur);

            $totalGajiBulanIni = round($totalGajiPokok + $totalTunjangan + $totalTransport + $totalMakan + $totalLembur);
        }else{
            $totalGajiBulanIni = 0;
        }
        

        // ====================================  TOTAL GAJI BULAN LALU  ============================
        $day   = date('d');
        $month = date('m');
        $year  = date('Y');

        if ($day >= 16) {
            $twoMontLater = \Carbon\Carbon::now()->subMonth(+1)->format('Y-m');
            // $oneMontLater = \Carbon\Carbon::now()->subMonth()->format('Y-m');

            $peroideStart = $twoMontLater.'-16';
            $peroideEnd   = $year.'-'.$month.'-15';
        }else if ($day <= 15) {
            $twoMontLater = \Carbon\Carbon::now()->subMonth(+2)->format('Y-m');
            // $oneMontLater = \Carbon\Carbon::now()->subMonth(+1)->format('Y-m');

            $peroideStart = $twoMontLater.'-16';
            $peroideEnd   = $year.'-'.$month.'-15';
        }

        $cek = DB::table('absen')->where('date', $peroideStart)->first();
        if ($cek) {
            $master =  Karyawan::withCount(['totalKerja' => function($query) use ($peroideStart, $peroideEnd) {
                             $query->whereDate('date', '>=', $peroideStart)->whereDate('date', '<=', $peroideEnd);
                          },
                          'totalOt' => function($query) use ($peroideStart, $peroideEnd) {
                             $query->select(DB::raw('SUM(type_ot)'))->whereDate('date', '>=', $peroideStart)->whereDate('date', '<=', $peroideEnd);
                          }])
                          ->with(['relPayroll' => function($query) use ($peroideStart, $peroideEnd) {
                             $query->where('periode_start', '>=', $peroideStart)->where('periode_end', '<=', $peroideEnd);
                          }]);
                          $result = $master->orderBy('id_karyawan', 'ASC')->get();

            $gajiPokok = [];
            $lembur    = [];
            foreach ($result as $i => $value) {
                $totalKerja[$i] = $value->total_kerja_count;
                $totalOT[$i]    = $value->total_ot_count;

                array_push($gajiPokok, (($value->harian == 0) ? (($value->bulanan / $value->relPayroll->periode_total_hk) * $totalKerja[$i]) : ($value->harian * $totalKerja[$i])));
                array_push($lembur, $totalOT[$i] * (($value->unit == 'Head Quarter') ? 250000 : 22619));
            }

            $totalGajiPokok = array_sum($gajiPokok);
            $totalTunjangan = DB::table('karyawan')->sum('tj_jabatan_skill');
            $totalTransport = DB::table('karyawan')->sum('transport');
            $totalMakan     = DB::table('karyawan')->sum('makan');
            $totalLembur    = array_sum($lembur);

            $totalGajiBulanLalu = round($totalGajiPokok + $totalTunjangan + $totalTransport + $totalMakan + $totalLembur);
        }else{
            $totalGajiBulanLalu = 0;
        }

        // ====================================  TOTAL USERS  ================================
        $totalUsers = DB::table('users')->pluck('id')->count();

        $dataResult = [
            'totalKaryawan'      => $totalKaryawan,
            'totalGajiBulanIni'  => $totalGajiBulanIni,
            'totalGajiBulanLalu' => $totalGajiBulanLalu,
            'totalUsers'         => $totalUsers,
        ];

        return Responses::sendResponse($dataResult, ' Retrieved Successfully');
    }

    public function showEhp(Request $request)
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
                      $EHP = $result;

        $dataResult = [
            'EHP' => $EHP,
        ];

        return Responses::sendResponse($dataResult, 'Account Retrieved Successfully');
    }
}