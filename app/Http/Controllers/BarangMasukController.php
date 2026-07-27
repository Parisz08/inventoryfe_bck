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
                    ->select('barang_masuk.id','barang_masuk.material_code','material_name','type','qty','note','date','barang_masuk.created_by','barang_masuk.created_at');
        if(!empty($request->input('material_code'))){
            $result = $master->where('barang_masuk.material_code', $request->material_code);
        }
        if(!empty($request->input('material_name'))){
            $result = $master->where('material_name', 'LIKE', "%".$request->material_name."%");
        }
        if(!empty($request->input('type'))){
            $result = $master->where('type', 'LIKE', "%".$request->type."%");
        }
        if(!empty($request->input('note'))){
            $result = $master->where('note', 'LIKE', "%".$request->note."%");
        }
        if(!empty($request->input('date'))){
            $date      = $request->input('date');
            $dateStart = date(substr($date, 0, 10));
            $dateEnd   = date(substr($date, -10));

            $result = $master->whereDate('date', '>=', $dateStart);
            $result = $master->whereDate('date', '<=', $dateEnd);
        }
        
        if (empty($request->input('material_code')) && empty($request->input('material_name')) && empty($request->input('type')) && empty($request->input('note')) && empty($request->input('date'))) {
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
                ->select('barang_masuk.id','barang_masuk.material_code','material_name','specification','type','unit','stock_barang','min_stock','storage_location','unit_price','image','qty','note','date','barang_masuk.created_by','barang_masuk.created_at')
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
            $material_code = 'WHSBCK'.$seq;
        }

        DB::transaction(function() use ($request, $material_code, $cekMaterial){
            // CREATE HISTORY BARANG MASUK
            $data                = new BarangMasuk;
            $data->material_code = $material_code;
            $data->qty           = $request->input('qty');
            $data->note          = ($request->input('note') == '') ? null : $request->input('note');
            $data->date          = $request->input('date');
            $data->created_by    = LoggedUser::get()['user']->full_name;
            $data->save();

            // INSERT IMAGE
            if ($request->hasFile('image')) {
                $attach    = $request->image;
                $original  = $attach->getClientOriginalName();
                $file      = pathinfo($original, PATHINFO_FILENAME);
                $extension = pathinfo($original, PATHINFO_EXTENSION);
                $filename  = $file.'_'.\Carbon\Carbon::now()->format('ymd_his').'.'.$extension;

                $attach->move(storage_path('image_barang'), $filename );
            }

            // CREATE OR UPDATE STOCK BARANG
            $data = StockBarang::updateOrCreate([
                'material_name' => $request->input('material_name'),
            ],[
                'material_code'    => $material_code,
                'material_name'    => $request->input('material_name'),
                'specification'    => $request->input('specification'),
                'type'             => $request->input('type'),
                'unit'             => $request->input('unit'),
                'stock_barang'     => $request->input('stock_barang'),
                'min_stock'        => $request->input('min_stock'),
                'storage_location' => $request->input('storage_location'),
                'image'            => (($request->hasFile('image')) ? $filename : $cekMaterial->image),
                'created_by'       => LoggedUser::get()['user']->full_name,
            ]);

            return Responses::sendResponse($data, 'Barang Masuk Created Successfully');
        });
    }

    public function update(Request $request, $id)
    {
        $validator = validator::make($request->all(), [
            'material_code'    => 'required',
            'material_name'    => 'required',
            'type'             => 'required',
            'unit'             => 'required',
            'min_stock'        => 'required',
            'storage_location' => 'required',
            'date'             => 'required',
        ]);

        if($validator->fails()){
            return Responses::sendError($validator->errors(), 'Validation Error');
        }

        DB::transaction(function() use ($request, $id){

            // CREATE HISTORY BARANG MASUK
            $data                = BarangMasuk::find($id);
            $data->material_code = $request->input('material_code');
            $data->qty           = $request->input('qty');
            $data->note          = ($request->input('note') == '') ? null : $request->input('note');
            $data->date          = $request->input('date');
            $data->created_by    = LoggedUser::get()['user']->full_name;
            $data->save();

            // CREATE OR UPDATE STOCK BARANG
            $oldFilename = StockBarang::where('material_code', $request->input('material_code'))->first();
            $data = StockBarang::updateOrCreate([
                'material_name' => $request->input('material_name'),
            ],[
                'material_code'    => $request->input('material_code'),
                'material_name'    => $request->input('material_name'),
                'specification'    => ($request->input('specification') == '') ? null : $request->input('specification'),
                'type'             => $request->input('type'),
                'unit'             => $request->input('unit'),
                'stock_barang'     => $request->input('stock_barang'),
                'min_stock'        => $request->input('min_stock'),
                'storage_location' => $request->input('storage_location'),
                'image'            => (($request->hasFile('image')) ? $filename : $oldFilename->image),
                'created_by'       => LoggedUser::get()['user']->full_name,
            ]);

            return Responses::sendResponse($data, 'Barang Masuk Updated Successfully');
        });
    }

    public function duplicate(Request $request)
    {
        $validator = validator::make($request->all(), [
            'material_code'    => 'required',
            'material_name'    => 'required',
            'type'             => 'required',
            'unit'             => 'required',
            'min_stock'        => 'required',
            'storage_location' => 'required',
            'date'             => 'required',
        ]);

        if($validator->fails()){
            return Responses::sendError($validator->errors(), 'Validation Error');
        }

        DB::transaction(function() use ($request){

            // CREATE HISTORY BARANG MASUK
            $data                = new BarangMasuk;
            $data->material_code = $request->input('material_code');
            $data->qty           = $request->input('qty');
            $data->note          = ($request->input('note') == '') ? null : $request->input('note');
            $data->date          = $request->input('date');
            $data->created_by    = LoggedUser::get()['user']->full_name;
            $data->save();

            // CREATE OR UPDATE STOCK BARANG
            $oldFilename = StockBarang::where('material_code', $request->input('material_code'))->first();
            $data = StockBarang::updateOrCreate([
                'material_name' => $request->input('material_name'),
            ],[
                'stock_barang'     => $request->input('stock_barang'),
            ]);

            return Responses::sendResponse($data, 'Barang Masuk Updated Successfully');
        });
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
}