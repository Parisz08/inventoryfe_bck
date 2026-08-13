<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Library\Responses;
use Illuminate\Support\Facades\DB;
use validator;
use App\Http\Traits\LoggedUser;
use App\Karyawan;
use App\StockBarang;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalItem         = DB::table('stock_barang')->count();
        $totalBarang       = DB::table('stock_barang')->sum('stock_barang');
        $totalBarangMasuk  = DB::table('barang_masuk')->sum('qty');
        $totalBarangKeluar = DB::table('barang_keluar')->sum('qty');

        $dataResult = [
            'totalItem'         => $totalItem,
            'totalBarang'       => $totalBarang,
            'totalBarangMasuk'  => $totalBarangMasuk,
            'totalBarangKeluar' => $totalBarangKeluar,
        ];

        return Responses::sendResponse($dataResult, ' Retrieved Successfully');
    }

    public function barangMinStock(Request $request)
    {
        $master = StockBarang::where('stock_barang', '<=', DB::raw('min_stock'));
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
            'data' => $data,
        ];

        return Responses::sendResponse($dataResult, 'Account Retrieved Successfully');
    }

    public function barangSeringTerpakai(Request $request)
    {
        $master = DB::table('barang_keluar')
                    ->leftJoin('stock_barang', 'barang_keluar.material_code', '=', 'stock_barang.material_code')
                    ->select('barang_keluar.material_code','material_name','type','unit',DB::raw('sum(qty) as qty'),'storage_location');

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
            $result = $master->groupBy('barang_keluar.material_code')->orderBy('qty', 'DESC')->get();
        }else{
            $result = $master->groupBy('barang_keluar.material_code')->orderBy('qty', 'DESC')->get();
        }

        $data  = $result;

        $dataResult = [
            'data' => $data,
        ];

        return Responses::sendResponse($dataResult, 'Account Retrieved Successfully');
    }
}