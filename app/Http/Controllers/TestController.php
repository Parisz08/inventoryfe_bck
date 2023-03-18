<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Library\Responses;
use Illuminate\Support\Facades\DB;
use validator;
use App\Http\Traits\LoggedUser;
use App\Karyawan;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class TestController extends Controller
{
    public function Index(Request $request)
    {
      // $to           = '2023-01-26';
      // $from         = '2023-01-30';
      // $range = new \DatePeriod( new \DateTime($to), new \DateInterval('P1D'), new \DateTime($from . ' +1 day'));

      // // INSERT OR UPDATE DATA ABSEN
      // $totalCuti = [];
      // $totalLBR  = [];
      // foreach ($range as $key => $value) {
      //     $date[$key] = $value->format('Y-m-d');  
      //     $day[$key]  = date('l', strtotime($value->format('Y-m-d')));
      //     $stsLibur = DB::table('absen')->where('id_karyawan')->where('date', $date[$key])->pluck('status')->first();

      //     if (($day[$key] == 'Saturday' || $day[$key] == 'Sunday' || $stsLibur == 'Holiday' )) {
      //       array_push($totalLBR, 1);
      //     }else{
      //       array_push($totalCuti, 1);
      //     }
      // }

      // echo array_sum($totalCuti) .'<br>'; 
      // echo array_sum($totalLBR);

      // return $date;
        $user            = new User;
        $user->full_name = 'SAMLAWI';
        $user->username  = 'noval';
        $user->password  = password_hash('adminnovalhr23', PASSWORD_BCRYPT);
        $user->role      = 'Admin';
        $user->status    = 'Aktif';
        $user->save();

        // return $peroideEnd;

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


        $peroideStart = '2022-12-16';
        $peroideEnd   = '2023-01-15';
        $nama_bank    = 'BANK BSI';

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

                      // if($nama_bank == 'BANK BSI')
                      // {
                        $result = $master->where('bank', 'Bank Syariah Indonesia')->orderBy('id_karyawan', 'ASC')->get();
                      // }else if($nama_bank == 'BANK MANDIRI')
                      // {
                      //   $result = $master->where('bank', 'Bank Mandiri')->orderBy('id_karyawan', 'ASC')->get();
                      // }else
                      // {
                      //   $result = $master->whereNotIn('bank', ['Bank Syariah Indonesia','Bank Mandiri'])->orderBy('id_karyawan', 'ASC')->get();
                      // }
                      
                      $payroll   = $result;

                    $totalG = [];
                    foreach ($payroll as $i => $row) {

                        $total = ((($row->harian == 0) ? (($row->bulanan / $row->relPayroll['periode_total_hk']) * $row->total_kerja_count) : ($row->harian * $row->total_kerja_count))
                                    + $row->tj_jabatan_skill + $row->transport + $row->makan + $row->total_ot_count * (($row->unit == 'Head Quarter') ? 250000 : 22619)
                                    - ( $row->relPayroll['piutang'] + $row->relPayroll['pinjaman'] + $row->jht + $row->jkm + $row->jkk + $row->jp + $row->jks ));

                        array_push($totalG, round($total, 0));
                    }

                    $totalGaji = array_sum($totalG);
    }
}