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
    // LOGIN
    $router->post('/auth/login', 'AuthController@login');
    $router->get('/auth/logout', 'AuthController@logout');
    $router->get('create_akun_default', 'AkunController@create_akun_default');
    // TEST
    $router->get('/test', 'TestController@index');

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
    // KARYAWAN
    $router->get('karyawan/index', 'KaryawanController@index');
    $router->get('karyawan/show/{id}', 'KaryawanController@show');
    $router->post('karyawan/create', 'KaryawanController@store');
    $router->post('karyawan/update/{id}', 'KaryawanController@update');
    $router->post('karyawan/delete/{id}', 'KaryawanController@destroy');
    $router->post('karyawan/change-password-foto', 'KaryawanController@changePassFoto');
    // ABSENSI
    $router->get('absensi/index', 'AbsensiController@index');
    $router->get('absensi/show/{id}', 'AbsensiController@show');
    $router->post('absensi/create', 'AbsensiController@store');
    $router->post('absensi/set-libur', 'AbsensiController@setLibur');
    $router->post('absensi/update-absen', 'AbsensiController@update');
    // PAYROLL
    $router->get('payroll/index', 'PayrollController@index');
    $router->get('payroll/show', 'PayrollController@show');
    $router->post('payroll/update-payroll', 'PayrollController@update');
    $router->get('payroll/send-slip', 'PayrollController@sendSlip');
    $router->get('payroll/get-bank', 'PayrollController@getBank');
    // APPROVAL CUTI
    $router->get('approval-cuti/index', 'ApprovalCutiController@index');
    $router->get('approval-cuti/show/{id}', 'ApprovalCutiController@show');
    $router->post('approval-cuti/create', 'ApprovalCutiController@store');
    $router->post('approval-cuti/update/{id}', 'ApprovalCutiController@update');
    $router->post('approval-cuti/delete/{id}', 'ApprovalCutiController@destroy');
    // APPROVAL LEMBUR
    $router->get('approval-lembur/search-anggota', 'ApprovalLemburController@searchAnggotaLembur');
    $router->get('approval-lembur/index', 'ApprovalLemburController@index');
    $router->get('approval-lembur/show', 'ApprovalLemburController@show');
    $router->post('approval-lembur/create', 'ApprovalLemburController@store');
    $router->post('approval-lembur/app-lembur/{code_spl}', 'ApprovalLemburController@appLembur');
    $router->post('approval-lembur/delete-anggota/{id}', 'ApprovalLemburController@deleteAnggota');
    // SETTING APPROVAL
    $router->get('setting-approval/index', 'SettingApprovalController@index');
    $router->get('setting-approval/show/{id}', 'SettingApprovalController@show');
    $router->post('setting-approval/create', 'SettingApprovalController@store');
    $router->post('setting-approval/update/{id}', 'SettingApprovalController@update');
    $router->post('setting-approval/delete/{id}', 'SettingApprovalController@destroy');
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
