<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Library\Responses;
use Illuminate\Support\Facades\DB;
use App\Karyawan;
use App\User;
use validator;
use App\Http\Traits\LoggedUser;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {

        $master = DB::table('karyawan');
        if(!empty($request->input('id_karyawan'))){
            $result = $master->where('id_karyawan', 'LIKE', "%".$request->id_karyawan."%");
        }
        if(!empty($request->input('nama'))){
            $result = $master->where('nama', 'LIKE', "%".$request->nama."%");
        }
        if(!empty($request->input('nik'))){
            $result = $master->where('nik', 'LIKE', "%".$request->nik."%");
        }
        if(!empty($request->input('jabatan'))){
            $result = $master->where('jabatan', 'LIKE', "%".$request->jabatan."%");
        }
        if(!empty($request->input('unit'))){
            $result = $master->where('unit', 'LIKE', "%".$request->unit."%");
        }
        if(!empty($request->input('status'))){
            $result = $master->where('status', 'LIKE', "%".$request->status."%");
        }
        
        if (empty($request->input('id_karyawan')) && empty($request->input('nama')) && empty($request->input('nik')) && empty($request->input('jabatan'))  && empty($request->input('unit'))  && empty($request->input('status'))) {
            $result = $master->orderBy('id_karyawan', 'ASC')->get();
        }else{
            $result = $master->orderBy('id_karyawan', 'ASC')->get();
        }

        $data  = $result;

        $dataResult = [
            'data'  => $data,
        ];

        if (count($data) == 0) {
            return Responses::sendError($dataResult, 'Akun Is Empty');
        }

        return Responses::sendResponse($dataResult, 'Akun Retrieved Successfully');
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
        // SEQUENCE
        $lastSeq = DB::table('karyawan')->pluck('id_karyawan')->last();
        if (empty($lastSeq)) {
            $seq = str_pad(1, 3, '0', STR_PAD_LEFT);
        }else{
            $sum = substr($lastSeq, -3) + 1;
            $seq = str_pad($sum, 3, '0', STR_PAD_LEFT);
        }

        $karyawanId = 'BCK-'.$seq;

        $data                   = new Karyawan;
        $data->nama             = $request->input('nama');
        $data->nik              = ($request->input('nik') == '') ? null : $request->input('nik');
        $data->jabatan          = $request->input('jabatan');
        $data->unit             = $request->input('unit');
        $data->status           = ($request->input('status') == '') ? null : $request->input('status');
        $data->harian           = $request->input('harian');
        $data->bulanan          = $request->input('bulanan');
        $data->tj_jabatan_skill = $request->input('tj_jabatan_skill');
        $data->transport        = $request->input('transport');
        $data->makan            = $request->input('makan');
        $data->bank             = $request->input('bank');
        $data->no_rek           = $request->input('no_rek');
        $data->an_rek           = $request->input('an_rek');
        $data->no_bpjs_tk       = $request->input('no_bpjs_tk');
        $data->no_bpjs_kes      = $request->input('no_bpjs_kes');
        $data->id_karyawan      = $karyawanId;
        $data->created_by       = LoggedUser::get()['user']->full_name;
        $data->save();

        // return Responses::sendResponse($data, 'Account Created Successfully');
    }

    public function update(Request $request, $id)
    {
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

    public function changePassFoto(Request $request)
    {     
        // JIKA ADA FOTO YANG DI RUBAH
        if ($request->hasFile('foto')) {
            $attach    = $request->foto;
            $original  = $attach->getClientOriginalName();
            $file      = pathinfo($original, PATHINFO_FILENAME);
            $extension = pathinfo($original, PATHINFO_EXTENSION);
            $filename  = $file.'.'.$extension;

            $attach->move(storage_path('foto_karyawan'), $filename );

            $data                = Karyawan::find($request->id_karyawan);
            $data->foto_karyawan = $filename;
            $data->updated_by    = LoggedUser::get()['user']->full_name;
            $data->save();
        }

        // JIKA PASSWORD DI RUBAH
        if (!empty($request->password)) {
            DB::table('users')->where('employee_id', $request->employee_id)->update([
                'password' => password_hash($request->password, PASSWORD_BCRYPT),
            ]);
        }

        return Responses::sendResponse(null, 'Change Data Successfully');
    }
}