<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Library\Responses;
use Illuminate\Support\Facades\DB;
use App\Karyawan;
use validator;
use App\Http\Traits\LoggedUser;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        $per_page = 100;

        if(!empty($request->input('search'))){
            $data = DB::table('karyawan')
                    ->where('nama', 'LIKE', "%".$request->search."%")
                    ->orWhere('unit', 'LIKE', "%".$request->search."%")
                    ->orderBy('nama', 'ASC')
                    ->paginate($per_page);

            $links = $data->appends(['search' => $request->search])->links();
        } else {
            $data = DB::table('karyawan')
                    ->orderBy('nama', 'ASC')
                    ->paginate($per_page);

            $links = $data->links();
        }

        $dataResult = [
            'data'  => $data,
            'links' => $links,
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
        // $validator = validator::make($request->all(), [
        //     'full_name'   => 'required',
        //     'username'    => 'required|unique:users,username',
        //     'password'    => 'required',
        //     'role'        => 'required',
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
        $data->gaji_pokok       = $request->input('gaji_pokok');
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
        $data->total_cuti       = $request->input('total_cuti');
        $data->id_karyawan      = $karyawanId;
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