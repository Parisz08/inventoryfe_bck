<?php

namespace App\Http\Controllers;

use App\Vendor;
use App\Http\Library\Responses;

class VendorController extends Controller
{
    public function index()
    {
        $data = Vendor::orderBy('name', 'asc')->get();
        return Responses::sendResponse($data, 'Vendor Retrieved Successfully');
    }
}