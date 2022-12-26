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
use JonnyW\PhantomJs\Client;

$router->get('/', function () use ($router) {
    return $router->app->version();
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
    $router->post('payroll/create', 'PayrollController@store');
    $router->post('payroll/update-absen', 'PayrollController@update');

$router->get('get-img', function () use ($router) {
    
    $client = Client::getInstance();
    $client->getEngine()->setPath('../bin/phantomjs.exe');
    
    $request  = $client->getMessageFactory()->createPdfRequest('http://google.com');
    $response = $client->getMessageFactory()->createResponse();
    $file = 'screenshot/tes.jpg';
    
    $request->setOutputFile($file);
    var_dump($response);
    
    $client->send($request, $response);

    // rename('../bin/file.jpg', 'screenshot/tes.jpg');

    // echo '<img src="screenshot/tes.jpg" alt="Screenshot" />';

});
