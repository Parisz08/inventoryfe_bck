<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Library\Responses;
use Illuminate\Support\Facades\DB;
use App\Karyawan;
use App\ApprovalLembur;
use App\Absen;
use validator;
use App\Http\Traits\LoggedUser;
use Illuminate\Support\Facades\Mail;

class ApprovalLemburController extends Controller
{
    public function searchAnggotaLembur(Request $request)
    {
        $data = DB::table('karyawan')
                ->where('nama', 'LIKE', "%".$request->search."%")
                ->orWhere('jabatan', 'LIKE', "%".$request->search."%")
                ->orWhere('unit', 'LIKE', "%".$request->search."%")
                ->orderBy('nama', 'ASC')
                ->get();

        if (count($data) == 0) {
            return Responses::sendError($data, 'Karyawan Is Empty');
        }

        return Responses::sendResponse($data, 'Karyawan Retrieved Successfully');
    }

    public function index(Request $request)
    {

        $data = DB::table('approval_lembur')
                ->where('id_karyawan', LoggedUser::get()['user']->employee_id)
                ->orderBy('created_at', 'DESC')
                ->get();

        $dataApproval = DB::table('approval_lembur')
                        ->select('id','created_by','created_at','code_spl','tanggal','actual_jam', DB::raw('count(code_spl) as total_anggota'))
                        ->where('approved_to_id', LoggedUser::get()['user']->employee_id)
                        ->where('status_approval', 'Waiting')
                        ->groupBy('code_spl')
                        ->orderBy('created_at', 'DESC')
                        ->get();

        // $bln = \Carbon\Carbon::now()->month;
        $totalJamLemburMonth  = DB::table('approval_lembur')
                                ->where('id_karyawan', LoggedUser::get()['user']->employee_id)
                                ->where('status_approval', 'Approved')
                                // ->whereMonth('tanggal', $bln)
                                ->sum('calculate_jam');

        $dataResult = [
            'data'                => $data,
            'dataApproval'        => $dataApproval,
            'totalJamLemburMonth' => $totalJamLemburMonth,
        ];

        return Responses::sendResponse($dataResult, 'Cuti Retrieved Successfully');
    }
    
    public function show(Request $request)
    {
        $data = DB::table('approval_lembur')
                ->where('code_spl', $request->code_spl)
                ->orderBy('created_at', 'DESC')
                ->get();

        if (is_null($data)) {
            return Responses::sendError($data, 'Data Is Empty');
        }

        return Responses::sendResponse($data, 'Data Retrieved Successfully');
    }

    public function store(Request $request)
    {     
        $dataKaryawan = DB::table('karyawan')->where('id_karyawan', $request->input('id_karyawan'))->first();
        $approvalTo   = DB::table('setting_approval')->where('id_karyawan_requester', $request->input('id_karyawan'))->first();

        setlocale(LC_ALL, 'IND');
        $jam       = $request->input('jam');
        $typeHari  = \Carbon\Carbon::parse($request->input('tanggal'))->formatLocalized('%A');
        $getStatus = DB::table('absen')->where('date', $request->input('tanggal'))->pluck('status')->first();
        if ($jam != 0) {
            $jam1 = 1;
            $jam2 = ($jam - 1);
            $calculate_jam = ($dataKaryawan->unit === 'Head Quarter') ? ($jam/8) : (($typeHari == 'Sabtu' || $typeHari == 'Minggu' || $getStatus == 'Holiday') ? ($jam * 2) : ($jam1 * 1.5 + $jam2 * 2));
        }else{
            $calculate_jam = 0;
        }

        $data                   = new ApprovalLembur;
        $data->code_spl         = $request->input('code_spl');
        $data->id_karyawan      = $request->input('id_karyawan');
        $data->nama             = $dataKaryawan->nama;
        $data->posisi           = $dataKaryawan->jabatan;
        $data->unit             = $dataKaryawan->unit;
        $data->hari             = $typeHari;
        $data->tanggal          = $request->input('tanggal');
        $data->actual_jam       = $request->input('jam');
        $data->calculate_jam    = $calculate_jam;
        $data->uraian_pekerjaan = $request->input('uraian_pekerjaan');

        $data->status_approval  = 'Waiting';
        $data->approved_to      = $approvalTo->nama_approver;
        $data->approved_to_id   = $approvalTo->id_karyawan_approver;
        $data->created_by       = LoggedUser::get()['user']->full_name;
        $data->save();

        // // SEND EMAIL TO APPROVER
        // $dataAnggota  = DB::table('approval_lembur')->where('code_spl', $request->input('code_spl'))->get();
        // for ($i=0; $i < count($dataAnggota); $i++) {
        //     $dataKaryawan  = DB::table('karyawan')->where('id_karyawan', $dataAnggota->id_karyawan)->get();


        //     $data = array('nama_approver'=> $approvalTo->nama_approver, 'nama_pembuat' => $request->dibuat_oleh, 'jabatan' => $dataRequester->jabatan, 'unit' => $dataRequester->unit, 'alasan' => $request->alasan_cuti, 'link_domain' => 'http://localhost:8080/detail-profile/'.$approvalTo->id_karyawan_approver);

        //     Mail::send('email/mailNotifLembur', $data, function($message) use ($email, $nama_pembuat, $nama_approver) {
        //         $message->to($email, $nama_approver)->subject('SURAT PERINTAH LEMBUR');
        //         $message->from('bckit22@gmail.com','BCK Notification');
        //     });
        // }

        return Responses::sendResponse($data, 'Created Successfully');
    }

    public function appLembur(Request $request, $code_spl)
    {
        // update status app lembur
        DB::table('approval_lembur')->where('code_spl', $code_spl)->update([
            'approved_by'     => LoggedUser::get()['user']->full_name,
            'approved_date'   =>  date('Y-m-d'),
            'status_approval' => $request->input('type')
        ]);

        // JIKA DI APPROVE MAKA UPDATE ABSEN
        if ($request->input('type') == 'Approved') {
            $dataAnggotaLembur = DB::table('approval_lembur')->where('code_spl', $code_spl)->get();

            // INSERT OR UPDATE DATA ABSEN
            foreach ($dataAnggotaLembur as $key => $value) {
                $id_karyawan[$key]   = $value->id_karyawan; 
                $date[$key]          = $value->tanggal; 
                $calculate_jam[$key] = $value->calculate_jam; 

                $data = Absen::updateOrCreate([
                    'id_karyawan' => $id_karyawan[$key],
                    'date'        => $date[$key],
                ],[
                    'type_ot'    => $calculate_jam[$key],
                    'updated_by' => LoggedUser::get()['user']->full_name,
                ]);
            }
        }

        return Responses::sendResponse($data, 'Approved Successfully');
    }

    public function deleteAnggota($id)
    {
        $data = ApprovalLembur::destroy($id);

        return Responses::sendResponse($data, 'Anggota Deleted Successfully');
    }
}