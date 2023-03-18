<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Library\Responses;
use Illuminate\Support\Facades\DB;
use App\Karyawan;
use App\ApprovalCuti;
use App\Absen;
use validator;
use App\Http\Traits\LoggedUser;
use Illuminate\Support\Facades\Mail;

class ApprovalCutiController extends Controller
{
    public function index(Request $request)
    {

        $data = DB::table('approval_cuti')
                ->where('id_karyawan', LoggedUser::get()['user']->employee_id)
                ->orderBy('created_at', 'DESC')
                ->get();

        $dataApproval = DB::table('approval_cuti')
                        ->where('approved_to_id', LoggedUser::get()['user']->employee_id)
                        ->where('status_approval', 'Waiting')
                        ->get();

        $year = \Carbon\Carbon::now()->year;
        $totalCuti  =   DB::table('approval_cuti')
                        ->where('id_karyawan', LoggedUser::get()['user']->employee_id)
                        ->where('type', 'Cuti')
                        ->where('status_approval', 'Approved')
                        ->whereYear('created_at', '=', $year)
                        ->sum('total_day');    
        $totalSakit =   DB::table('approval_cuti')
                        ->where('id_karyawan', LoggedUser::get()['user']->employee_id)
                        ->where('type', 'Sakit')
                        ->where('status_approval', 'Approved')
                        ->whereYear('created_at', '=', $year)
                        ->sum('total_day');

        $dataResult = [
            'data'         => $data,
            'dataApproval' => $dataApproval,
            'totalCuti'    => $totalCuti,
            'totalSakit'   => $totalSakit,
        ];

        return Responses::sendResponse($dataResult, 'Cuti Retrieved Successfully');
    }
    
    public function show($id)
    {
        $data = Karyawan::where('id_karyawan', $id)->first();

        if (is_null($data)) {
            return Responses::sendError($data, 'Account Is Empty');
        }

        return Responses::sendResponse($data, 'Account Retrieved Successfully');
    }

    public function store(Request $request)
    {     
        $approvalTo    = DB::table('setting_approval')->where('id_karyawan_requester', $request->input('id_karyawan'))->first();
        $dataRequester = DB::table('karyawan')->where('id_karyawan', $request->input('id_karyawan'))->first();
        $dataApprover  = DB::table('karyawan')->where('id_karyawan', $approvalTo->id_karyawan_approver)->first();

        // jika type SAKIT
        if ($request->hasFile('bukti')) {
            $attach    = $request->bukti;
            $original  = $attach->getClientOriginalName();
            $file      = pathinfo($original, PATHINFO_FILENAME);
            $extension = pathinfo($original, PATHINFO_EXTENSION);
            $filename  = $file.'.'.$extension;
            $attach->move(storage_path('foto_bukti_sakit'), $filename );
        }

        // calculate total day cuti
        $range     = new \DatePeriod( new \DateTime($request->dari_tanggal), new \DateInterval('P1D'), new \DateTime($request->sampai_tanggal . ' +1 day'));
        $totalCuti = [];
        $totalLBR  = [];
        foreach ($range as $key => $value) {
          $date[$key] = $value->format('Y-m-d');  
          $day[$key]  = date('l', strtotime($value->format('Y-m-d')));
          $stsLibur   = DB::table('absen')->where('id_karyawan', $request->id_karyawan)->where('date', $date[$key])->pluck('status')->first();

          if (($day[$key] == 'Saturday' || $day[$key] == 'Sunday' || $stsLibur == 'Holiday' )) {
            array_push($totalLBR, 1);
          }else{
            array_push($totalCuti, 1);
          }
        }

        $data                  = new ApprovalCuti;
        $data->id_karyawan     = $request->input('id_karyawan');
        $data->dibuat_oleh     = $request->input('dibuat_oleh');
        $data->type            = $request->input('type');
        $data->total_day       = array_sum($totalCuti);
        if ($request->hasFile('bukti')) {
            $data->bukti = $filename;
        }
        $data->alasan_cuti     = $request->input('alasan_cuti');
        $data->dari_tanggal    = $request->input('dari_tanggal');
        $data->sampai_tanggal  = $request->input('sampai_tanggal');
        $data->status_approval = 'Waiting';
        $data->approved_to     = $approvalTo->nama_approver;
        $data->approved_to_id  = $approvalTo->id_karyawan_approver;
        $data->save();

        // SEND EMAIL TO APPROVER
        $nama_pembuat  = $request->dibuat_oleh;
        $nama_approver = $approvalTo->nama_approver;
        $email         = $dataApprover->email;
        $data = array('nama_approver'=> $approvalTo->nama_approver, 'nama_pembuat' => $request->dibuat_oleh, 'jabatan' => $dataRequester->jabatan, 'unit' => $dataRequester->unit, 'alasan' => $request->alasan_cuti, 'link_domain' => 'http://localhost:8080/detail-profile/'.$approvalTo->id_karyawan_approver);

        Mail::send('email/mailNotifCuti', $data, function($message) use ($email, $nama_pembuat, $nama_approver) {
            $message->to($email, $nama_approver)->subject('PENGAJUAN CUTI/ IZIN '. $nama_pembuat);
            $message->from('bckit22@gmail.com','BCK Notification');
        });

        return Responses::sendResponse($data, 'Created Successfully');
    }

    public function update(Request $request, $id)
    {
        // update status app cuti
        $data                  = ApprovalCuti::find($id);
        $data->approved_by     = LoggedUser::get()['user']->full_name;
        $data->approved_date   = date('Y-m-d');
        $data->status_approval = $request->input('type');
        $data->save();

        $dataRequester = DB::table('approval_cuti')->where('id', $id)->first();
        $dataKaryawan  = DB::table('karyawan')->where('id_karyawan', $dataRequester->id_karyawan)->first();

        // JIKA DI APPROVE MAKA UPDATE ABSEN
        if ($request->input('type') == 'Approved') {
            $range = new \DatePeriod( new \DateTime($dataRequester->dari_tanggal), new \DateInterval('P1D'), new \DateTime($dataRequester->sampai_tanggal . ' +1 day'));

            // INSERT OR UPDATE DATA ABSEN
            foreach ($range as $key => $value) {
                $date[$key] = $value->format('Y-m-d');  
                $day[$key]  = date('l', strtotime($value->format('Y-m-d')));
                $stsLibur   = DB::table('absen')->where('id_karyawan', $dataRequester->id_karyawan)->where('date', $date[$key])->pluck('status')->first();  

                $data = Absen::updateOrCreate([
                    'id_karyawan' => $dataRequester->id_karyawan,
                    'date'        => $date[$key],
                ],[
                    'id_karyawan'     => $dataRequester->id_karyawan,
                    'date'            => $date[$key],
                    'type_hk'         => ($day[$key] == 'Saturday' || $day[$key] == 'Sunday' || $stsLibur == 'Holiday') ? '' : (($dataRequester->type == 'Cuti') ? 'C' : 'S'),
                    'updated_by'      => LoggedUser::get()['user']->full_name,
                ]);
            }
        }

        // SEND EMAIL TO REQUESTER
        $nama_pembuat  = $dataRequester->dibuat_oleh;
        $email         = $dataKaryawan->email;
        $data = array('nama_pembuat' => $nama_pembuat, 'nama_approver' => $dataRequester->approved_to, 'status_approval' => $request->type, 'link_domain' => 'http://localhost:8080/detail-profile/'.$dataRequester->id_karyawan);

        Mail::send('email/mailNotifAppCuti', $data, function($message) use ($email, $nama_pembuat) {
            $message->to($email, $nama_pembuat)->subject('KONFIRMASI PENGAJUAN CUTI/ IZIN');
            $message->from('bckit22@gmail.com','BCK Notification');
        });

        return Responses::sendResponse($data, 'Approved Successfully');
    }

    public function destroy($id)
    {
        $data = Karyawan::destroy($id);

        return Responses::sendResponse($data, 'Account Deleted Successfully');
    }
}