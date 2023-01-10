<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Library\Responses;
use Illuminate\Support\Facades\DB;
use validator;
use App\Http\Traits\LoggedUser;
use App\Karyawan;
use App\Absen;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class TestController extends Controller
{
    public function Index(Request $request)
    {
        $peroideStart = '2022-11-16';
        $peroideEnd   = '2022-12-15';

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
                      // ->select('nama','unit','id_karyawan')
                      if(!empty($request->input('nama'))){
                            $result = $master->where('nama', 'LIKE', "%".$request->nama."%");
                      }

                      if (empty($request->input('nama'))) {
                            $result = $master->orderBy('id_karyawan', 'ASC')->get();
                      }else{
                            $result = $master->orderBy('id_karyawan', 'ASC')->get();
                      }
                      $payroll = $result;

        $dataResult = [
            'payroll' => $payroll,
        ];

        return $dataResult

        // $userkey    = '3d38baf08d69';
        // $passkey    = '148af3e842ee7745688f7aa4';
        // $telepon    =  $value->no_hp;
        // $image_link =  'https://besimpro.bck.co.id/storage/BCK-002-2022-11-16-2022-12-15.png';
        // $caption    = 'Hallo ini adalah notifikasi slip gaji PT. Buana Centra Karya bulan November 2022 atas nama Muhazir, apabila ada pertanyaan lebih lanjut silahkan hubungi 089696925665';
        // $url        = 'https://console.zenziva.net/wareguler/api/sendWAFile/';
        // $curlHandle = curl_init();
        // curl_setopt($curlHandle, CURLOPT_URL, $url);
        // curl_setopt($curlHandle, CURLOPT_HEADER, 0);
        // curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
        // curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
        // curl_setopt($curlHandle, CURLOPT_TIMEOUT,30);
        // curl_setopt($curlHandle, CURLOPT_POST, 1);
        // curl_setopt($curlHandle, CURLOPT_POSTFIELDS, array(
        //     'userkey' => $userkey,
        //     'passkey' => $passkey,
        //     'to' => $telepon,
        //     'link' => $image_link,
        //     'caption' => $caption
        // ));
        // $results = json_decode(curl_exec($curlHandle), true);
        // curl_close($curlHandle);

        // return $results;
    }
}