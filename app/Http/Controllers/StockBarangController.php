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
        $data = DB::table('stock_barang')
                ->select('id','material_code','material_name','specification','type','unit','stock_barang','min_stock','storage_location','unit_price','image','created_by','created_at')
                ->where('id', $id)
                ->first();

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
            'min_stock'        => 'required',
            'storage_location' => 'required',
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
            $data->note          = ($request->input('note') == '') ? null : $request->input('note');
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
                'specification'    => ($request->input('specification') == '') ? null : $request->input('specification'),
                'type'             => $request->input('type'),
                'unit'             => $request->input('unit'),
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
        ]);

        if($validator->fails()){
            return Responses::sendError($validator->errors(), 'Validation Error');
        }

        DB::transaction(function() use ($request, $id){

            // INSERT IMAGE
            if ($request->hasFile('image')) {
                $attach    = $request->image;
                $original  = $attach->getClientOriginalName();
                $file      = pathinfo($original, PATHINFO_FILENAME);
                $extension = pathinfo($original, PATHINFO_EXTENSION);
                $filename  = $file.'.'.$extension;

                $attach->move(storage_path('image_barang'), $filename );
            }

            // CREATE OR UPDATE STOCK BARANG
            $oldFilename = StockBarang::where('material_code', $request->input('material_code'))->first();

            $data                   = StockBarang::find($id);
            $data->material_code    = $request->input('material_code');
            $data->material_name    = $request->input('material_name');
            $data->specification    = $request->input('specification');
            $data->type             = $request->input('type');
            $data->unit             = $request->input('unit');
            $data->min_stock        = $request->input('min_stock');
            $data->storage_location = $request->input('storage_location');
            $data->image            = (($request->hasFile('image')) ? $filename : $oldFilename->image);
            $data->created_by       = LoggedUser::get()['user']->full_name;
            $data->save();

            return Responses::sendResponse($data, 'Barang Masuk Updated Successfully');
        });
    }

    public function destroy($id)
    {
        $data = StockBarang::destroy($id);

        return Responses::sendResponse($data, 'Stock Barang Deleted Successfully');
    }
}