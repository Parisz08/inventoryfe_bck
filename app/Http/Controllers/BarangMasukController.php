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

class BarangMasukController extends Controller
{
    public function index(Request $request)
    {
        $master = DB::table('barang_masuk')
                    ->leftJoin('stock_barang', 'barang_masuk.material_code', '=', 'stock_barang.material_code')
                    ->select('barang_masuk.id','barang_masuk.material_code','material_name','qty','note','date','barang_masuk.created_by','barang_masuk.created_at');
        if(!empty($request->input('material_code'))){
            $result = $master->where('material_code', $request->material_code);
        }
        if(!empty($request->input('note'))){
            $result = $master->where('note', 'LIKE', "%".$request->note."%");
        }
        if(!empty($request->input('date'))){
            $result = $master->where('date', 'LIKE', "%".$request->date."%");
        }
        
        if (empty($request->input('material_code')) && empty($request->input('note')) && empty($request->input('date'))) {
            $result = $master->orderBy('barang_masuk.created_at', 'DESC')->get();
        }else{
            $result = $master->orderBy('barang_masuk.created_at', 'DESC')->get();
        }

        $data  = $result;

        $dataResult = [
            'data'  => $data,
        ];

        if (count($data) == 0) {
            return Responses::sendError($dataResult, 'Barang Masuk Is Empty');
        }

        return Responses::sendResponse($dataResult, 'Barang Masuk Retrieved Successfully');
    }
    
    public function show($id)
    {
        $data = DB::table('barang_masuk')
                ->leftJoin('stock_barang', 'barang_masuk.material_code', '=', 'stock_barang.material_code')
                ->select('barang_masuk.id','barang_masuk.material_code','material_name','type','unit','stock_barang','min_stock','storage_location','qty','note','date','barang_masuk.created_by','barang_masuk.created_at')
                ->where('barang_masuk.id', $id)
                ->first();

        if (is_null($data)) {
            return Responses::sendError($data, 'Barang Masuk Is Empty');
        }

        return Responses::sendResponse($data, 'Barang Masuk Retrieved Successfully');
    }

    public function cekMaterial(Request $request)
    {
        $data = StockBarang::where('material_code', $request->input('material_code'))->orWhere('material_name', $request->input('material_name'))->first();

        if (is_null($data)) {
            return Responses::sendError($data, 'Material Is Empty');
        }

        return Responses::sendResponse($data, 'Material Retrieved Successfully');
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

        // SEQUENCE
        $lastSeq = DB::table('stock_barang')->max(DB::raw('substr(material_code, -4)'));
        // jika jo belum ada
        if (empty($lastSeq)) {
            return $seq = str_pad(1, 4, '0', STR_PAD_LEFT);
        }else{
            $sum = substr($lastSeq, -4) + 1;
            $seq = str_pad($sum, 4, '0', STR_PAD_LEFT);
        }

        $cekMaterial = StockBarang::where('material_code', $request->input('material_code'))->orWhere('material_name', $request->input('material_name'))->first();
        if ($cekMaterial) {
            $material_code = $cekMaterial->material_code;
        }else{
            $material_code = 'INVBCK'.$seq;
        }

        // CREATE HISTORY BARANG MASUK
        $data                = new BarangMasuk;
        $data->material_code = $material_code;
        $data->qty           = $request->input('qty');
        $data->note          = ($request->input('note') == '') ? null : $request->input('note');
        $data->date          = $request->input('date');
        $data->created_by    = LoggedUser::get()['user']->full_name;
        $data->save();

        // CREATE OR UPDATE STOCK BARANG
        $data = StockBarang::updateOrCreate([
            'material_name' => $request->input('material_name'),
        ],[
            'material_code'    => $material_code,
            'material_name'    => $request->input('material_name'),
            'type'             => $request->input('type'),
            'unit'             => $request->input('unit'),
            'stock_barang'     => $request->input('stock_barang'),
            'min_stock'        => $request->input('min_stock'),
            'storage_location' => $request->input('storage_location'),
            'created_by'       => LoggedUser::get()['user']->full_name,
        ]);

        return Responses::sendResponse($data, 'Barang Masuk Created Successfully');
    }

    public function update(Request $request, $id)
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

        // CREATE HISTORY BARANG MASUK
        $data                = BarangMasuk::find($id);
        $data->material_code = $request->input('material_code');
        $data->qty           = $request->input('qty');
        $data->note          = ($request->input('note') == '') ? null : $request->input('note');
        $data->date          = $request->input('date');
        $data->created_by    = LoggedUser::get()['user']->full_name;
        $data->save();

        // CREATE OR UPDATE STOCK BARANG
        $data = StockBarang::updateOrCreate([
            'material_name' => $request->input('material_name'),
        ],[
            'material_code'    => $request->input('material_code'),
            'material_name'    => $request->input('material_name'),
            'type'             => $request->input('type'),
            'unit'             => $request->input('unit'),
            'stock_barang'     => $request->input('stock_barang'),
            'min_stock'        => $request->input('min_stock'),
            'storage_location' => $request->input('storage_location'),
            'created_by'       => LoggedUser::get()['user']->full_name,
        ]);

        return Responses::sendResponse($data, 'Barang Masuk Updated Successfully');
    }

    public function destroy($id)
    {
        $getQtyBM       = DB::table('barang_masuk')->where('id', $id)->select('material_code','qty')->first();
        $getStockBarang = DB::table('stock_barang')->where('material_code', $getQtyBM->material_code)->select('stock_barang')->first();

        // UPDATE STOCK BARANG
        if ($getStockBarang) {
            DB::table('stock_barang')->where('material_code', $getQtyBM->material_code)->update([ 'stock_barang' => ($getStockBarang->stock_barang - $getQtyBM->qty) ]);
        }

        $data = BarangMasuk::destroy($id);

        return Responses::sendResponse($data, 'Barang Masuk Deleted Successfully');
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