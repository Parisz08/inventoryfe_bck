<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Library\Responses;
use Illuminate\Support\Facades\DB;
use App\User;
use validator;
use App\Http\Traits\LoggedUser;

class AkunController extends Controller
{
    public function index(Request $request)
    {
        $per_page = 100;

        if(!empty($request->input('search'))){
            $data = DB::table('users')
                    ->where('username', 'LIKE', "%".$request->search."%")
                    ->orderBy('full_name', 'ASC')
                    ->paginate($per_page);

            $links = $data->appends(['search' => $request->search])->links();
        } else {
            $data = DB::table('users')
                    ->orderBy('full_name', 'ASC')
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

    public function indexProfile(Request $request)
    {
        $data = DB::table('users')
                ->where('username', LoggedUser::get()['user']->username)
                // ->orderBy('created_at', 'DESC')
                ->get();

        if (!$data) {
            return Responses::sendError($data, 'Profile Is Empty');
        }

        return Responses::sendResponse($data, 'Profile Retrieved Successfully');
    }

    public function show($id)
    {
        $data = User::find($id);

        if (is_null($data)) {
            return Responses::sendError($data, 'Account Is Empty');
        }

        return Responses::sendResponse($data, 'Account Retrieved Successfully');
    }

    public function store(Request $request)
    {     
        $validator = validator::make($request->all(), [
            'full_name'   => 'required',
            'username'    => 'required|unique:users,username',
            'password'    => 'required',
            'role'        => 'required',
        ]);

        if($validator->fails()){
            return Responses::sendError($validator->errors(), 'Validation Error');
        }

        // if ($request->hasFile('image')) {
        //     $attach    = $request->image;
        //     $original  = $attach->getClientOriginalName();
        //     $file      = pathinfo($original, PATHINFO_FILENAME);
        //     $extension = pathinfo($original, PATHINFO_EXTENSION);
        //     $filename  = $file.'_'.\Carbon\Carbon::now()->format('ymd_his').'.'.$extension;

        //     $attach->move(storage_path('image_user'), $filename );
        // }

        $data              = new User;
        $data->full_name   = $request->input('full_name');
        $data->employee_id = ($request->input('employee_id') == '') ? null : $request->input('employee_id');
        $data->username    = $request->input('username');
        $data->password    = password_hash($request->input('password'), PASSWORD_BCRYPT);   
        $data->role        = $request->input('role');
        $data->status      = "Active";
        $data->save();

        return Responses::sendResponse($data, 'Account Created Successfully');
    }

    public function update(Request $request, $id)
    {
        $validator = validator::make($request->all(), [
            'full_name' => 'required',
            'username'  => 'required|unique:users,username,'.$id,
            'role'      => 'required',
        ]);

        if($validator->fails()){
            return Responses::sendError($validator->errors(), 'Validation Error');       
        }

        // if ($request->hasFile('image')) {
        //     $attach    = $request->image;
        //     $original  = $attach->getClientOriginalName();
        //     $file      = pathinfo($original, PATHINFO_FILENAME);
        //     $extension = pathinfo($original, PATHINFO_EXTENSION);
        //     $filename  = $file.'_'.\Carbon\Carbon::now()->format('ymd_his').'.'.$extension;
            
        //     $attach->move(storage_path('image_user'), $filename );
        // }

        $data              = User::find($id);
        $data->full_name   = $request->input('full_name');
        $data->employee_id = ($request->input('employee_id') == '') ? null : $request->input('employee_id');
        $data->username    = $request->input('username');
        if (!empty($request->input('password'))) {
            $data->password   = password_hash($request->input('password'), PASSWORD_BCRYPT);  
        }
        $data->role    = $request->input('role');
        $data->company = ($request->input('company') == '') ? null : $request->input('company');
        $data->status  = $request->input('status');
        $data->save();

        return Responses::sendResponse($data, 'Account Updated Successfully');
    }

    public function destroy($id)
    {
        $data = User::destroy($id);

        return Responses::sendResponse($data, 'Account Deleted Successfully');
    }

    public function create_akun_default()
    {
        $user            = new User;
        $user->full_name = 'Admin';
        $user->username  = 'admin';
        $user->password  = password_hash('123456', PASSWORD_BCRYPT);
        $user->role      = 'Admin';
        $user->status    = 'Aktif';
        $user->save();
    }
}