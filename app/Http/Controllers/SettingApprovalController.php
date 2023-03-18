<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Library\Responses;
use Illuminate\Support\Facades\DB;
use App\Karyawan;
use App\SettingApproval;
use validator;
use App\Http\Traits\LoggedUser;

class SettingApprovalController extends Controller
{
    public function index(Request $request)
    {

        // $master = DB::table('karyawan');
        // if(!empty($request->input('id_karyawan'))){
        //     $result = $master->where('id_karyawan', 'LIKE', "%".$request->id_karyawan."%");
        // }
        // if(!empty($request->input('nama'))){
        //     $result = $master->where('nama', 'LIKE', "%".$request->nama."%");
        // }
        // if(!empty($request->input('nik'))){
        //     $result = $master->where('nik', 'LIKE', "%".$request->nik."%");
        // }
        // if(!empty($request->input('jabatan'))){
        //     $result = $master->where('jabatan', 'LIKE', "%".$request->jabatan."%");
        // }
        // if(!empty($request->input('unit'))){
        //     $result = $master->where('unit', 'LIKE', "%".$request->unit."%");
        // }
        // if(!empty($request->input('status'))){
        //     $result = $master->where('status', 'LIKE', "%".$request->status."%");
        // }
        
        // if (empty($request->input('id_karyawan')) && empty($request->input('nama')) && empty($request->input('nik')) && empty($request->input('jabatan'))  && empty($request->input('unit'))  && empty($request->input('status'))) {
        //     $result = $master->orderBy('id_karyawan', 'ASC')->get();
        // }else{
        //     $result = $master->orderBy('id_karyawan', 'ASC')->get();
        // }

        // $data  = $result;
        $data = DB::table('setting_approval')
                ->leftJoin('karyawan', 'setting_approval.id_karyawan_requester', '=', 'karyawan.id_karyawan')
                
                ->get();

        $dataResult = [
            'data' => $data,
        ];

        if (count($data) == 0) {
            return Responses::sendError($dataResult, 'Cuti Is Empty');
        }

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
       $getRequester = DB::table('karyawan')->where('nama', $request->input('requester'))->first();
       $getApprover  = DB::table('karyawan')->where('nama', $request->input('approver'))->first();

        $data                        = new SettingApproval;
        $data->id_karyawan_requester = $getRequester->id_karyawan;
        $data->nama_requester        = $getRequester->nama;
        $data->id_karyawan_approver  = $getApprover->id_karyawan;
        $data->nama_approver         = $getApprover->nama;
        $data->created_by            = LoggedUser::get()['user']->full_name;
        $data->save();

        // return Responses::sendResponse($data, 'Account Created Successfully');
    }

    public function update(Request $request, $id)
    {
        // $validator = validator::make($request->all(), [
        //     'full_name' => 'required',
        //     'username'  => 'required|unique:users,username,'.$id,
        //     'role'      => 'required',
        // ]);

        // if($validator->fails()){
        //     return Responses::sendError($validator->errors(), 'Validation Error');       
        // }

        // if ($request->hasFile('image')) {
        //     $attach    = $request->image;
        //     $original  = $attach->getClientOriginalName();
        //     $file      = pathinfo($original, PATHINFO_FILENAME);
        //     $extension = pathinfo($original, PATHINFO_EXTENSION);
        //     $filename  = $file.'_'.\Carbon\Carbon::now()->format('ymd_his').'.'.$extension;
            
        //     $attach->move(storage_path('image_user'), $filename );
        // }

        $data                   = Karyawan::find($id);
        $data->nama             = $request->input('nama');
        $data->jabatan          = $request->input('jabatan');
        $data->unit             = $request->input('unit');
        $data->harian           = $request->input('harian');
        $data->bulanan          = $request->input('bulanan');
        $data->tj_jabatan_skill = $request->input('tj_jabatan_skill');
        $data->transport        = $request->input('transport');
        $data->makan            = $request->input('makan');
        $data->status           = ($request->input('status') == '') ? null : $request->input('status');

        $data->bank   = $request->input('bank');
        $data->no_rek = $request->input('no_rek');
        $data->an_rek = $request->input('an_rek');

        $data->no_bpjs_tk  = $request->input('no_bpjs_tk');
        $data->no_bpjs_kes = $request->input('no_bpjs_kes');
        $data->upah_bpjs   = $request->input('upah_bpjs');
        $data->jht         = $request->input('jht');
        $data->jkm         = $request->input('jkm');
        $data->jkk         = $request->input('jkk');
        $data->jp          = $request->input('jp');
        $data->jks         = $request->input('jks');

        $data->nik        = ($request->input('nik') == '') ? null : $request->input('nik');
        $data->no_hp      = $request->input('no_hp');
        $data->email      = $request->input('email');
        $data->total_cuti = $request->input('total_cuti');
        $data->updated_by = LoggedUser::get()['user']->full_name;
        $data->save();

        return Responses::sendResponse($data, 'Account Updated Successfully');
    }

    public function destroy($id)
    {
        $data = Karyawan::destroy($id);

        return Responses::sendResponse($data, 'Account Deleted Successfully');
    }

    public function create_akun_default()
    {
        $user             = new Karyawan;
        $user->username   = 'admin';
        $user->password   = password_hash('123456', PASSWORD_BCRYPT);
        $user->save();
    }
}