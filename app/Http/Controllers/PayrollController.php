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
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Mail;

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
                      }])
                      ->with(['relPayroll' => function($query) use ($peroideStart, $peroideEnd, $idKaryawan) {
                         $query->where('periode_start', '>=', $peroideStart)->where('periode_end', '<=', $peroideEnd)->where('id_karyawan', $idKaryawan);
                      }]);
                      if(!empty($request->input('nama'))){
                            $result = $master->where('nama', 'LIKE', "%".$request->nama."%");
                      }

                      if (empty($request->input('nama'))) {
                            $result = $master->where('id_karyawan', $idKaryawan)->orderBy('id_karyawan', 'ASC')->first();
                      }else{
                            $result = $master->where('id_karyawan', $idKaryawan)->orderBy('id_karyawan', 'ASC')->first();
                      }
                      $showPayroll = $result;

        if (is_null($showPayroll)) {
            return Responses::sendError($showPayroll, 'Show Payroll Is Empty');
        }

        return Responses::sendResponse($showPayroll, 'Show Payroll Retrieved Successfully');
    }

    public function update(Request $request)
    {
        $data             = Payroll::find($request->id);
        $data->piutang    = $request->piutang;
        $data->pinjaman   = $request->pinjaman;
        $data->kekurangan = $request->kekurangan;
        if ($request->status_tf == 1 || $request->status_tf == 'true') {
            $data->status_tf  = 'true';
        }else if($request->status_tf == 0 || $request->status_tf == 'false'){
            $data->status_tf  = 'false';
        }
        if ($request->send_slip == 1 || $request->send_slip == 'true') {
            $data->send_slip  = 'true';
        }else if($request->send_slip == 0 || $request->send_slip == 'false') {
            $data->send_slip  = 'false';
        }
        $data->updated_by = LoggedUser::get()['user']->full_name;
        $data->save();

        return Responses::sendResponse($data, 'Absen Updated Successfully');
    }

    public function sendSlip(Request $request)
    {
        // GET PERIODE
        $peroideStart = $request->periode_start;
        $peroideEnd   = $request->periode_end;
        // $idKaryawan   = DB::table('karyawan')->orderBy('id_karyawan', 'ASC')->pluck('id_karyawan')->all();

        // // =========================== GENERATE SLIP ======================
        // // LOOPING DATA KARYAWAN
        // foreach ($idKaryawan as $i => $karyawan) {
        //     $id_karyawan[$i] = $karyawan;

        //     // SCREENSHOT SLIP GAJI
        //     Browsershot::url('http://localhost:8080/detail-slip/'.$id_karyawan[$i].'/'.$peroideStart.'/'.$peroideEnd)
        //         ->setOption('landscape', true)
        //         ->windowSize(1000, 900)
        //         // ->clip(1000, 1000, 1000, 1110)
        //         ->waitUntilNetworkIdle()
        //         ->save(storage_path('slip_gaji/'.$id_karyawan[$i].'-'.$peroideStart.'-'.$peroideEnd.'.png'));
        // }

        // ============================ SEND SLIP ==========================
        // AMBIL DATA KARYAWAN YANG SEND SLIP NYA TRUE
        $noHPKaryawan = DB::table('karyawan')
                        ->leftJoin('payroll', 'karyawan.id_karyawan', '=', 'payroll.id_karyawan')
                        ->select('karyawan.id_karyawan', 'email','nama')
                        ->where('payroll.send_slip', 'true')
                        ->where('payroll.periode_start', $peroideStart)
                        ->where('payroll.periode_end', $peroideEnd)
                        ->whereNotNull('email')
                        ->orderBy('karyawan.id_karyawan', 'ASC')
                        ->get();

        foreach ($noHPKaryawan as $key => $value) {
            setlocale(LC_ALL, 'IND');
            $idKaryawan   = $value->id_karyawan;
            $email        = $value->email;
            $nama_lengkap = $value->nama;
            $monthPeriode = strtoupper(\Carbon\Carbon::parse($peroideStart)->formatLocalized('%B %Y'));

            $masterFix =  Karyawan::withCount(['totalKerja' => function($query) use ($peroideStart, $peroideEnd, $idKaryawan) {
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

            
            // $img_slip     = storage_path('slip_gaji/'.$value->id_karyawan.'-'.$peroideStart.'-'.$peroideEnd.'.png');

            $relPay = json_decode($masterFix, true);
            $data   = array('data' => $masterFix, 'relPay' =>$relPay['rel_payroll'], 'periode' => $monthPeriode);
                      // return array($monthPeriode);

            Mail::send('email/mailSendSlip', $data, function($message) use ($email, $nama_lengkap, $monthPeriode) {
                $message->to($email, $nama_lengkap)->subject('SLIP GAJI '. $monthPeriode. ' - '. $nama_lengkap);
                $message->from('bckit22@gmail.com','BCK Notification');
            });
        }

        return Responses::sendResponse(null, 'Berhasil Mengirim Slip Gaji');
    }

    public function getBank(Request $request)
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
                      }])
                      ->with(['relPayroll' => function($query) use ($peroideStart, $peroideEnd) {
                         $query->where('periode_start', '>=', $peroideStart)->where('periode_end', '<=', $peroideEnd);
                      }]);

                      if($request->nama_bank == 'BANK BSI')
                      {
                        $result = $master->where('bank', 'Bank Syariah Indonesia')->orderBy('id_karyawan', 'ASC')->get();
                      }else if($request->nama_bank == 'BANK MANDIRI')
                      {
                        $result = $master->where('bank', 'Bank Mandiri')->orderBy('id_karyawan', 'ASC')->get();
                      }else
                      {
                        $result = $master->whereNotIn('bank', ['Bank Syariah Indonesia','Bank Mandiri'])->orderBy('id_karyawan', 'ASC')->get();
                      }

                    $payroll   = $result;
                      
                    // generate total gaji
                    $totalG = [];
                    foreach ($payroll as $i => $row) {

                        $total = ((($row->harian == 0) ? (($row->bulanan / $row->relPayroll['periode_total_hk']) * $row->total_kerja_count) : ($row->harian * $row->total_kerja_count))
                                    + $row->tj_jabatan_skill + $row->transport + $row->makan + $row->total_ot_count * (($row->unit == 'Head Quarter') ? 250000 : 22619)
                                    - ( $row->relPayroll['piutang'] + $row->relPayroll['pinjaman'] + $row->jht + $row->jkm + $row->jkk + $row->jp + $row->jks ));

                        array_push($totalG, round($total, 0));
                    }
                    $totalGaji = array_sum($totalG);

        $dataResult = [
            'totalGaji' => $totalGaji,
            'payroll'   => $payroll,
        ];

        if (count($payroll) == 0) {
            return Responses::sendError($dataResult, 'Payroll Is Empty');
        }

        return Responses::sendResponse($dataResult, 'Payroll Retrieved Successfully');
    }
}