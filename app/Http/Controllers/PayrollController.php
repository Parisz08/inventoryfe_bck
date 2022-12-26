<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Library\Responses;
use Illuminate\Support\Facades\DB;
use App\Karyawan;
use App\Absen;
use validator;
use App\Http\Traits\LoggedUser;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $peroideStart = $request->periode_start;
        $peroideEnd   = $request->periode_end;

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
                      $payroll = $result;

        $dataResult = [
            'payroll' => $payroll,
        ];

        if (count($payroll) == 0) {
            return Responses::sendError($dataResult, 'Payroll Is Empty');
        }

        return Responses::sendResponse($dataResult, 'Payroll Retrieved Successfully');
    }
    
    public function show(Request $request)
    {
        $idKaryawan   = $request->id_karyawan;
        $peroideStart = $request->periode_start;
        $peroideEnd   = $request->periode_end;

        $master =  Karyawan::withCount(['totalKerja' => function($query) use ($peroideStart, $peroideEnd, $idKaryawan) {
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
                      }]);
                      if(!empty($request->input('nama'))){
                            $result = $master->where('nama', 'LIKE', "%".$request->nama."%");
                      }

                      if (empty($request->input('nama'))) {
                            $result = $master->where('id_karyawan', $idKaryawan)->orderBy('nama', 'ASC')->first();
                      }else{
                            $result = $master->where('id_karyawan', $idKaryawan)->orderBy('nama', 'ASC')->first();
                      }
                      $showPayroll = $result;

        if (is_null($showPayroll)) {
            return Responses::sendError($showPayroll, 'Show Payroll Is Empty');
        }

        return Responses::sendResponse($showPayroll, 'Show Payroll Retrieved Successfully');
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
            $data->type_ot = ($unitUser === 'Head Quarter') ? ($jam/8) : (($typeHari == 'Sab' || $typeHari == 'Min') ? ($jam * 2) : (1 * 1.5 + $jam * 2));
        }
        $data->save();

        return Responses::sendResponse($data, 'Absen Updated Successfully');
    }
}