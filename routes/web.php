<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return view('email/mailNotifLembur');
});


// ================================= FOR NO LOGIN ==========================================================
    // TEST
    $router->get('test', 'TestController@index');

    // LOGIN
    $router->post('/auth/login', 'AuthController@login');
    $router->get('/auth/logout', 'AuthController@logout');
    $router->get('create_akun_default', 'AkunController@create_akun_default');

    // EXPORT EXCEL
    $router->get('export-excel/karyawan', 'ExportExcelController@karyawan');
    $router->get('export-excel/absen', 'ExportExcelController@absen');
    $router->get('export-excel/payroll', 'ExportExcelController@payroll');
    $router->get('export-excel/payroll-bank', 'ExportExcelController@payrollBank');
    // PDF
    $router->get('print-pdf/ehp', 'PrintPdfController@printEhp');
    $router->get('print-slip-gaji', 'PrintPdfController@printSlipGaji');
    $router->get('print-pdf/spl', 'PrintPdfController@printSpl');

// ================================= FOR LOGIN ==========================================================
$router->group(['middleware' => 'jwt.tymon'], function () use ($router){
    // DASHBOARD
    $router->get('dashboard/index', 'DashboardController@index');
    $router->get('dashboard/show-ehp', 'DashboardController@showEhp');
    // BARANG MASUK
    $router->get('barang-masuk/index', 'BarangMasukController@index');
    $router->get('barang-masuk/show/{id}', 'BarangMasukController@show');
    $router->get('barang-masuk/cek-material', 'BarangMasukController@cekMaterial');
    $router->post('barang-masuk/create', 'BarangMasukController@store');
    $router->post('barang-masuk/update/{id}', 'BarangMasukController@update');
    $router->post('barang-masuk/delete/{id}', 'BarangMasukController@destroy');
    // BARANG KELUAR
    $router->get('barang-keluar/index', 'BarangKeluarController@index');
    $router->get('barang-keluar/search-material', 'BarangKeluarController@searchMaterial');
    $router->get('barang-keluar/get-material', 'BarangKeluarController@getMaterial');
    $router->post('barang-keluar/create', 'BarangKeluarController@store');
    $router->post('barang-keluar/update/{id}', 'BarangKeluarController@update');
    $router->post('barang-keluar/delete/{id}', 'BarangKeluarController@destroy');
    // STOCK BARANG
    $router->get('stock-barang/index', 'StockBarangController@index');
    $router->get('stock-barang/show/{id}', 'StockBarangController@show');
    $router->post('stock-barang/create', 'StockBarangController@store');
    $router->post('stock-barang/update/{id}', 'StockBarangController@update');
    $router->post('stock-barang/delete/{id}', 'StockBarangController@destroy');
    // AKUN
    $router->get('akun/index', 'AkunController@index');
    $router->get('akun/index-profile', 'AkunController@indexProfile');
    $router->get('akun/show/{id}', 'AkunController@show');
    $router->post('akun/create', 'AkunController@store');
    $router->post('akun/update/{id}', 'AkunController@update');
    $router->post('akun/delete/{id}', 'AkunController@destroy');
    $router->get('akun/create_akun_default', 'AkunController@create_akun_default');
    // IMPORT
    $router->post('import/import-data-karyawan', 'ImportController@importKaryawan');
    $router->post('import/setting-approval', 'ImportController@importSettingApproval');
    $router->post('import/import-data-users', 'ImportController@importUsers');

});
