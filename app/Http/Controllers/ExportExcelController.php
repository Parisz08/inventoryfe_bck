<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PayrollExport;

class ExportExcelController extends Controller
{
    
    public function payroll(Request $request)
    {
        return Excel::download(new PayrollExport($request), 'Data Payroll.xlsx');
    }
}