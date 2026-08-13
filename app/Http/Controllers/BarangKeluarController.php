<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Library\Responses;
use Illuminate\Support\Facades\DB;
use App\BarangKeluar;
use App\StockBarang;
use App\User;
use validator;
use App\Http\Traits\LoggedUser;

class BarangKeluarController extends Controller
{
    public function index(Request $request)
    {
        $master = DB::table('barang_keluar')
                    ->leftJoin('stock_barang', 'barang_keluar.material_code', '=', 'stock_barang.material_code')
                    ->select('barang_keluar.id','barang_keluar.material_code','material_name','type','unit','qty','description','divisi','no_sj','date','diserahkan','disetujui','diterima','barang_keluar.created_by','barang_keluar.created_at');

        if(!empty($request->input('no_sj'))){
            $result = $master->where('no_sj', $request->no_sj);
        }
        if(!empty($request->input('material_code'))){
            $result = $master->where('barang_keluar.material_code', $request->material_code);
        }
        if(!empty($request->input('material_name'))){
            $result = $master->where('material_name', 'LIKE', "%".$request->material_name."%");
        }
        if(!empty($request->input('type'))){
            $result = $master->where('type', $request->type);
        }
        if(!empty($request->input('unit'))){
            $result = $master->where('unit', $request->unit);
        }
        if(!empty($request->input('description'))){
            $result = $master->where('description', 'LIKE', "%".$request->description."%");
        }
        if(!empty($request->input('divisi'))){
            $result = $master->where('divisi', $request->divisi);
        }
        if(!empty($request->input('date'))){
            $date      = $request->input('date');
            $dateStart = date(substr($date, 0, 10));
            $dateEnd   = date(substr($date, -10));

            $result = $master->whereDate('date', '>=', $dateStart);
            $result = $master->whereDate('date', '<=', $dateEnd);
        }
        
        if (empty($request->input('no_sj')) && empty($request->input('material_code')) && empty($request->input('material_name')) && empty($request->input('type')) && empty($request->input('unit')) && empty($request->input('description')) && empty($request->input('divisi')) && empty($request->input('date'))) {
            $result = $master->orderBy('barang_keluar.created_at', 'DESC')->get();
        }else{
            $result = $master->orderBy('barang_keluar.created_at', 'DESC')->get();
        }

        $data  = $result;

        $dataResult = [
            'data'  => $data,
        ];

        if (count($data) == 0) {
            return Responses::sendError($dataResult, 'Barang Keluar Is Empty');
        }

        return Responses::sendResponse($dataResult, 'Barang Keluar Retrieved Successfully');
    }

    public function searchMaterial(Request $request)
    {
        $data = StockBarang::where('material_code', $request->search)
                             // ->where('stock_barang', '<', 1)
                             ->orWhere('material_name', 'LIKE', "%".$request->search."%")
                             ->orWhere('type', $request->search)
                             ->orWhere('storage_location', $request->search)
                             ->get();

        if (is_null($data)) {
            return Responses::sendError($data, 'Material Is Empty');
        }

        return Responses::sendResponse($data, 'Material Retrieved Successfully');
    }
    
    public function getMaterial(Request $request)
    {
        $data = DB::table('barang_keluar')
                ->leftJoin('stock_barang', 'barang_keluar.material_code', '=', 'stock_barang.material_code')
                ->select('barang_keluar.id','barang_keluar.material_code','material_name','unit','stock_barang','qty','divisi','description','date','diserahkan','disetujui','diterima','barang_keluar.created_by','barang_keluar.created_at')
                ->where('barang_keluar.no_sj', $request->no_sj)
                ->get();

        if (is_null($data)) {
            return Responses::sendError($data, 'Barang Keluar Is Empty');
        }

        return Responses::sendResponse($data, 'Barang Keluar Retrieved Successfully');
    }

    public function store(Request $request)
    {     
        $validator = validator::make($request->all(), [
            'id'         => 'required',
            'no_sj'      => 'required',
            'divisi'     => 'required',
            'date'       => 'required',
            'diserahkan' => 'required',
            'disetujui'  => 'required',
            'diterima'   => 'required',
        ]);

        if($validator->fails()){
            return Responses::sendError($validator->errors(), 'Validation Error');
        }

        // CREATE HISTORY BARANG KELUAR
        $cariMaterial = DB::table('stock_barang')->where('id', $request->id)->first();

        $data                = new BarangKeluar;
        $data->material_code = $cariMaterial->material_code;
        $data->no_sj         = $request->no_sj;
        $data->date          = $request->date;
        $data->divisi        = $request->divisi;
        $data->qty           = 1;
        $data->diserahkan    = $request->diserahkan;
        $data->disetujui     = $request->disetujui;
        $data->diterima      = $request->diterima;
        $data->created_by    = LoggedUser::get()['user']->full_name;
        $data->save();

        // UPDATE STOCK BARANG
        $getDataStock    = DB::table('stock_barang')->where('material_code', $cariMaterial->material_code)->first();

        DB::table('stock_barang')->where('material_code', $cariMaterial->material_code)->update([
            'stock_barang' => ($getDataStock->stock_barang - 1),
            'created_by'   => LoggedUser::get()['user']->full_name,
        ]);

        return Responses::sendResponse($data, 'Barang Keluar Created Successfully');
    }

    public function update(Request $request, $id)
    {
        $validator = validator::make($request->all(), [
            'qty' => 'required',
        ]);

        if($validator->fails()){
            return Responses::sendError($validator->errors(), 'Validation Error');
        }

        // UPDATE DULU KE STOCK BARANG AWAL
        $getBarangKeluar = DB::table('barang_keluar')->where('id', $id)->first();
        $getDataStock    = DB::table('stock_barang')->where('material_code', $getBarangKeluar->material_code)->first();

        DB::table('stock_barang')->where('material_code', $getDataStock->material_code)->update([
            'stock_barang' => ($getDataStock->stock_barang + $getBarangKeluar->qty),
            'updated_by'   => LoggedUser::get()['user']->full_name,
        ]);

        // UPDATE QTY HISTORY BARANG KELUAR
        $data             = BarangKeluar::find($id);
        $data->qty        = $request->input('qty');
        $data->updated_by = LoggedUser::get()['user']->full_name;
        $data->save();

        // UPDATE STOCK BARANG
        $getBarangKeluar = DB::table('barang_keluar')->where('id', $id)->first();
        $getDataStock    = DB::table('stock_barang')->where('material_code', $getBarangKeluar->material_code)->first();

        DB::table('stock_barang')->where('material_code', $getDataStock->material_code)->update([
            'stock_barang' => ($getDataStock->stock_barang - $getBarangKeluar->qty),
            'updated_by'   => LoggedUser::get()['user']->full_name,
        ]);

        return Responses::sendResponse($data, 'Barang Keluar Updated Successfully');
    }

    public function updateDesc(Request $request, $id)
    {
        $validator = validator::make($request->all(), [
            'description' => 'required',
        ]);

        if($validator->fails()){
            return Responses::sendError($validator->errors(), 'Validation Error');
        }

        // UPDATE DESC HISTORY BARANG KELUAR
        $data              = BarangKeluar::find($id);
        $data->description = $request->input('description');
        $data->updated_by  = LoggedUser::get()['user']->full_name;
        $data->save();

        return Responses::sendResponse($data, 'Barang Keluar Updated Successfully');
    }

    public function destroy($id)
    {
        $getQtyBM       = DB::table('barang_keluar')->where('id', $id)->select('material_code','qty')->first();
        $getStockBarang = DB::table('stock_barang')->where('material_code', $getQtyBM->material_code)->select('stock_barang')->first();

        // UPDATE STOCK BARANG
        if ($getStockBarang) {
            DB::table('stock_barang')->where('material_code', $getQtyBM->material_code)->update([ 'stock_barang' => ($getStockBarang->stock_barang + $getQtyBM->qty) ]);
        }

        $data = BarangKeluar::destroy($id);

        return Responses::sendResponse($data, 'Barang Keluar Deleted Successfully');
    }
}