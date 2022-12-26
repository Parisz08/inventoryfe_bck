<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Library\Responses;
use Illuminate\Support\Facades\DB;
use App\Karyawan;
use App\Absen;
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
                            $result = $master->orderBy('nama', 'ASC')->get();
                      }else{
                            $result = $master->orderBy('nama', 'ASC')->get();
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

        foreach ($dataKaryawan as $i => $karyawan) {
            $id_karyawan[$i] = $karyawan;

            foreach ($period as $key => $value) {
                $date[$key] = $value->format('Y-m-d');  
                $day[$key]  = date('l', strtotime($value->format('Y-m-d')));  

                $cek = DB::table('absen')->where('id_karyawan', $id_karyawan[$i])->where('date', $date[$key])->first();

                if (empty($cek)) {
                    $data              = new Absen;
                    $data->id_karyawan = $id_karyawan[$i];
                    $data->date        = $date[$key];
                    if (($day[$key] == 'Saturday' || $day[$key] == 'Sunday')) {
                        $data->type_hk = '';
                    }else{
                        $data->type_hk = 1;
                    }
                    $data->save(); 
                }
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

        return Responses::sendResponse($data, 'Absen Created Successfully');
    }

    public function update(Request $request)
    {
        $unitUser = $request->unit_user;
        $typeHari = $request->type_hari;
        // return $unitUser;
        $data = Absen::find($request->id);
        if ($request->type == 'HK') {
            $data->type_hk = $request->value;
        }else{
            $jam = $request->value;
            if ($jam != 0) {
                $data->type_ot = ($unitUser === 'Head Quarter') ? ($jam/8) : (($typeHari == 'Sab' || $typeHari == 'Min') ? ($jam * 2) : (1 * 1.5 + $jam * 2));
            }else{
                $data->type_ot = 0;
            }
        }
        $data->save();

        return Responses::sendResponse($data, 'Absen Updated Successfully');
    }
}