<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Library\Responses;
use Illuminate\Support\Facades\DB;
use validator;
use App\Http\Traits\LoggedUser;
use App\Karyawan;
use App\Absen;
use Carbon\Carbon;
use JonnyW\PhantomJs\Client;

class TestController extends Controller
{
    public function Index(Request $request)
    {

        $client = Client::getInstance();
        $client->getEngine()->setPath('../bin/Phantomjs.exe');

        $width  = 800;
        $height = 600;
        $top    = 0;
        $left   = 0;
        
        $request = $client->getMessageFactory()->createCaptureRequest('http://localhost/digajiin_be/public/get-img', 'GET');
        $request->setOutputFile(storage_path('Surat_Jalan.jpg'));
        $request->setViewportSize($width, $height);
        $request->setCaptureDimensions($width, $height, $top, $left);

        $response = $client->getMessageFactory()->createResponse();

        // Send the request
        $client->send($request, $response);





        // $userkey = '3d38baf08d69';
        // $passkey = '148af3e842ee7745688f7aa4';
        // $telepon = '089678966461';
        // $image_link = 'https://besimpro.bck.co.id/storage/Slip.jpeg';
        // $caption  = 'Hallo ini adalah notifikasi slip gaji PT. Buana Centra Karya bulan November 2022 atas nama Muhazir, apabila ada pertanyaan lebih lanjut silahkan hubungi 089696925665';
        // $url = 'https://console.zenziva.net/wareguler/api/sendWAFile/';
        // $curlHandle = curl_init();
        // curl_setopt($curlHandle, CURLOPT_URL, $url);
        // curl_setopt($curlHandle, CURLOPT_HEADER, 0);
        // curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
        // curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
        // curl_setopt($curlHandle, CURLOPT_TIMEOUT,30);
        // curl_setopt($curlHandle, CURLOPT_POST, 1);
        // curl_setopt($curlHandle, CURLOPT_POSTFIELDS, array(
        //     'userkey' => $userkey,
        //     'passkey' => $passkey,
        //     'to' => $telepon,
        //     'link' => $image_link,
        //     'caption' => $caption
        // ));
        // $results = json_decode(curl_exec($curlHandle), true);
        // curl_close($curlHandle);

        // return $results;
    }
}