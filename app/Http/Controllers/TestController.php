<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Library\Responses;
use Illuminate\Support\Facades\DB;
use validator;
use App\Http\Traits\LoggedUser;
use App\Karyawan;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TestController extends Controller
{
    public function Index(Request $request)
    {
      $qrcode = QrCode::size(400)->generate('azir');

        echo $qrcode;
    }
}