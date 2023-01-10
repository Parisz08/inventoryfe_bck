<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Library\Responses;
use DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\KaryawanImport;

class ImportController extends Controller
{
    public function importKaryawan(Request $request)
    {
        try {
            Excel::import(new KaryawanImport, $request->file('import_data'));
            return Responses::sendResponse('Ok', 'Import Successfully');

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
             $failures = $e->failures();
             
             foreach ($failures as $failure) {
                $row       = $failure->row(); // row that went wrong
                $attribute = $failure->attribute(); // either heading key (if using heading row concern) or column index
                $errors    = $failure->errors()[0]; // Actual error messages from Laravel validator
                $values    = $failure->values()[$attribute]; // The values of the row that has failed.

                $dataResult[] = [
                    'row'       => $row,
                    'attribute' => $attribute,
                    'errors'    => $errors,
                    'values'    => $values,
                ];
            }

            return Responses::sendError($dataResult, 'Import Failed');
        }
    }
}
