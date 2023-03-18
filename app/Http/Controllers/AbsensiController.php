<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Library\Responses;
use Illuminate\Support\Facades\DB;
use App\Karyawan;
use App\Absen;
use App\Payroll;
use validator;
use App\Http\Traits\LoggedUser;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $peroideStart = $request->periode_start;
        $peroideEnd   = $request->periode_end;

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
                      // ->select('nama','unit','id_karyawan')
                      if(!empty($request->input('nama'))){
                            $result = $master->where('nama', 'LIKE', "%".$request->nama."%");
                      }

                      if (empty($request->input('nama'))) {
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

        if (count($data) == 0) {
            return Responses::sendError($dataResult, 'Absen Is Empty');
        }

        return Responses::sendResponse($dataResult, 'Absen Retrieved Successfully');
    }
    
    public function show($id)
    {
        $data = Absen::find($id);

        if (is_null($data)) {
            return Responses::sendError($data, 'Absen Is Empty');
        }

        return Responses::sendResponse($data, 'Absen Retrieved Successfully');
    }

    public function store(Request $request)
    {     
        $validator = validator::make($request->all(), [
            'periode_start' => 'required',
            'periode_end'   => 'required',
        ]);

        if($validator->fails()){
            return Responses::sendError($validator->errors(), 'Validation Error');
        }

        $dataKaryawan = DB::table('karyawan')->pluck('id_karyawan')->all();
        $peroideStart = $request->input('periode_start');
        $peroideEnd   = $request->input('periode_end');
        $period       = new \DatePeriod( new \DateTime($peroideStart), new \DateInterval('P1D'), new \DateTime($peroideEnd . ' +1 day'));

        // LOOPING DATA KARYAWAN
        foreach ($dataKaryawan as $i => $karyawan) {
            $id_karyawan[$i] = $karyawan;

            // LOOPING DATA TANGGAL
            $periodeTotalHK = [];
            foreach ($period as $key => $value) {
                $date[$key] = $value->format('Y-m-d');  
                $day[$key]  = date('l', strtotime($value->format('Y-m-d')));  

                // INSERT DATA ABSEN DAN PENGECEKAN AGAR TIDAK DUPLICATE
                $cek = DB::table('absen')->where('id_karyawan', $id_karyawan[$i])->where('date', $date[$key])->first();
                if (empty($cek)) {
                    $data              = new Absen;
                    $data->id_karyawan = $id_karyawan[$i];
                    $data->date        = $date[$key];
                    if (($day[$key] == 'Saturday' || $day[$key] == 'Sunday')) {
                        $data->type_hk = '';
                    }else{
                        $data->type_hk = 1;
                        array_push($periodeTotalHK, 1);
                    }
                    $data->created_by = LoggedUser::get()['user']->full_name;
                    $data->save(); 
                }
            }

            // INSERT DATA PAYROLL DAN PENGECEKAN AGAR TIDAK DUPLICATE
            $cek = DB::table('payroll')->where('id_karyawan', $id_karyawan[$i])->where('periode_start', $peroideStart)->where('periode_end', $peroideEnd)->first();
            if (empty($cek)) {
                $payroll                   = new Payroll;
                $payroll->id_karyawan      = $id_karyawan[$i];
                $payroll->periode_start    = $peroideStart;
                $payroll->periode_end      = $peroideEnd;
                $payroll->periode_total_hk = array_sum($periodeTotalHK);
                $payroll->send_slip        = 'true';
                $payroll->created_by       = LoggedUser::get()['user']->full_name;
                $payroll->save();
            }
        }


        // if ($request->type == 'HK') {
        //     $type_hk = 1;
        // }elseif($request->type == 'A'){
        //     $type_hk = 0;
        // }elseif($request->type == 'S'){
        //     $type_hk = 'S';
        // }elseif($request->type == 'I'){
        //     $type_hk = 'I';
        // }elseif($request->type == 'C'){
        //     $type_hk = 'C';
        // }

        // $data              = new Absen;
        // $data->id_karyawan = $request->input('id_karyawan');
        // $data->date        = $request->input('date');
        // $data->type_hk     = $type_hk;
        // $data->save();

        return Responses::sendResponse(null, 'Absen Created Successfully');
    }

    public function setLibur(Request $request)
    {
        $peroideStart = $request->input('periode_start');
        $peroideEnd   = $request->input('periode_end');

        // update peroide total kerja
        $ambilTotalHK =  DB::table('payroll')->where('periode_start', $peroideStart)->where('periode_end', $peroideEnd)->pluck('periode_total_hk')->first();
        DB::table('payroll')->where('periode_start', $peroideStart)->where('periode_end', $peroideEnd)->update([
            'periode_total_hk' => ($ambilTotalHK - 1),
            'updated_by'       => LoggedUser::get()['user']->full_name
        ]);

        // update tanggal absen
        $data = DB::table('absen')->whereDate('date', $request->tanggal)->update([
                    'type_hk'    => null,
                    'status'     => 'Holiday',
                    'updated_by' => LoggedUser::get()['user']->full_name
                ]);

        return Responses::sendResponse(NULL, 'Absen Updated Successfully');
    }

    public function update(Request $request)
    {
        $unitUser = $request->unit_user;
        $typeHari = $request->type_hari;
        
        $getStatus = DB::table('absen')->where('id', $request->id)->pluck('status')->first();
        $data = Absen::find($request->id);
        if ($request->type == 'HK') {
            $data->type_hk = $request->value;
        }else{
            $jam = $request->value;
            if ($jam != 0) {
                $jam1 = 1;
                $jam2 = ($jam - 1);
                $data->type_ot = ($unitUser === 'Head Quarter') ? ($jam/8) : (($typeHari == 'Sab' || $typeHari == 'Min' || $getStatus == 'Holiday') ? ($jam * 2) : ($jam1 * 1.5 + $jam2 * 2));
            }else{
                $data->type_ot = 0;
            }
        }
        $data->updated_by = LoggedUser::get()['user']->full_name;
        $data->save();

        return Responses::sendResponse($data, 'Absen Updated Successfully');
    }
}