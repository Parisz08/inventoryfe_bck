<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KaryawanExport;
use App\Exports\AbsenExport;
use App\Exports\PayrollExport;
use App\Exports\PayrollBankExport;

class ExportExcelController extends Controller
{
    
    public function karyawan(Request $request)
    {
        return Excel::download(new KaryawanExport($request), 'Data Karyawan.xlsx');
    }

    public function absen(Request $request)
    {
        return Excel::download(new AbsenExport($request), 'Data Absen.xlsx');
    }

    public function payroll(Request $request)
    {
        return Excel::download(new PayrollExport($request), 'Data Payroll.xlsx');
    }

    public function payrollBank(Request $request)
    {
        return Excel::download(new PayrollBankExport($request), 'Data Payroll Per Bank.xlsx');
    }

}