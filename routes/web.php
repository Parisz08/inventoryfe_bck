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
use Spatie\Browsershot\Browsershot;

$router->get('/', function () use ($router) {
    return view('email/mailSendSlip');
});
$router->get('/test', 'TestController@index');

    // KARYAWAN
    $router->get('karyawan/index', 'KaryawanController@index');
    $router->get('karyawan/show/{id}', 'KaryawanController@show');
    $router->post('karyawan/create', 'KaryawanController@store');
    $router->post('karyawan/update/{id}', 'KaryawanController@update');
    $router->post('karyawan/delete/{id}', 'KaryawanController@destroy');
    // ABSENSI
    $router->get('absensi/index', 'AbsensiController@index');
    $router->get('absensi/show/{id}', 'AbsensiController@show');
    $router->post('absensi/create', 'AbsensiController@store');
    $router->post('absensi/update-absen', 'AbsensiController@update');
    // PAYROLL
    $router->get('payroll/index', 'PayrollController@index');
    $router->get('payroll/show', 'PayrollController@show');
    $router->post('payroll/update-payroll', 'PayrollController@update');
    $router->get('payroll/send-slip', 'PayrollController@sendSlip');
    // EXPORT EXCEL
    $router->get('export-excel/payroll', 'ExportExcelController@payroll');
    // IMPORT
    $router->post('import/import-data-karyawan', 'ImportController@importKaryawan');
