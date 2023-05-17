<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Library\Responses;
use Illuminate\Support\Facades\DB;
use App\BarangMasuk;
use App\StockBarang;
use App\User;
use validator;
use App\Http\Traits\LoggedUser;

class StockBarangController extends Controller
{
    public function index(Request $request)
    {

        $master = StockBarang::withCount(['totalBarangMasuk' => function($query) {
                        $query->select(DB::raw('SUM(qty)'));
                    },
                    'totalBarangKeluar' => function($query) {
                        $query->select(DB::raw('SUM(qty)'));
                    }]);
        if(!empty($request->input('material_code'))){
            $result = $master->where('material_code', $request->material_code);
        }
        if(!empty($request->input('material_name'))){
            $result = $master->where('material_name', 'LIKE', "%".$request->material_name."%");
        }
        if(!empty($request->input('type'))){
            $result = $master->where('type', 'LIKE', "%".$request->type."%");
        }
        if(!empty($request->input('unit'))){
            $result = $master->where('unit', 'LIKE', "%".$request->unit."%");
        }
        if(!empty($request->input('storage_location'))){
            $result = $master->where('storage_location', 'LIKE', "%".$request->storage_location."%");
        }
        
        if (empty($request->input('material_code')) && empty($request->input('material_name')) && empty($request->input('type')) && empty($request->input('unit')) && empty($request->input('storage_location')) ) {
            $result = $master->orderBy('material_name', 'ASC')->get();
        }else{
            $result = $master->orderBy('material_name', 'ASC')->get();
        }

        $data  = $result;

        $dataResult = [
            'data'  => $data,
        ];

        if (count($data) == 0) {
            return Responses::sendError($dataResult, 'Stock Barang Is Empty');
        }

        return Responses::sendResponse($dataResult, 'Stock Barang Retrieved Successfully');
    }
    
    public function show($id)
    {
        $data = BarangMasuk::where('id', $id)->first();

        if (is_null($data)) {
            return Responses::sendError($data, 'Stock Barang Is Empty');
        }

        return Responses::sendResponse($data, 'Stock Barang Retrieved Successfully');
    }

    public function store(Request $request)
    {     
        $validator = validator::make($request->all(), [
            'material_code'    => 'required',
            'material_name'    => 'required',
            'type'             => 'required',
            'unit'             => 'required',
            'qty'              => 'required',
            'min_stock'        => 'required',
            'storage_location' => 'required',
            'date'             => 'required',
        ]);

        if($validator->fails()){
            return Responses::sendError($validator->errors(), 'Validation Error');
        }

        $material_code = 'INVBCK001';

        // CREATE HISTORY Stock Barang
        $data                = new BarangMasuk;
        $data->material_code = $material_code;
        $data->qty           = $request->input('qty');
        $data->note          = ($request->input('note') == '') ? null : $request->input('note');
        $data->date          = $request->input('date');
        // $data->created_by    = LoggedUser::get()['user']->full_name;
        $data->save();

        // CREATE OR UPDATE STOCK BARANG
        $data = StockBarang::updateOrCreate([
            'material_name' => $request->input('material_name'),
        ],[
            'material_code'    => $material_code,
            'material_name'    => $request->input('material_name'),
            'type'             => $request->input('type'),
            'unit'             => $request->input('unit'),
            'stock_barang'     => $request->input('qty'),
            'min_stock'        => $request->input('min_stock'),
            'storage_location' => $request->input('storage_location'),
            // 'updated_by'       => LoggedUser::get()['user']->full_name,
        ]);

        return Responses::sendResponse($data, 'Stock Barang Created Successfully');
    }

    public function update(Request $request, $id)
    {
        $validator = validator::make($request->all(), [
            'qty'  => 'required',
            'date' => 'required',
        ]);

        if($validator->fails()){
            return Responses::sendError($validator->errors(), 'Validation Error');
        }

        $data             = BarangMasuk::find($id);
        $data->qty        = $request->input('qty');
        $data->note       = ($request->input('note') == '') ? null : $request->input('note');
        $data->date       = $request->input('date');
        $data->updated_by = LoggedUser::get()['user']->full_name;
        $data->save();

        return Responses::sendResponse($data, 'Stock Barang Updated Successfully');
    }

    public function destroy($id)
    {
        $data = BarangMasuk::destroy($id);

        return Responses::sendResponse($data, 'Stock Barang Deleted Successfully');
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

            $data                = BarangMasuk::find($request->id_karyawan);
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