<?php

namespace App\Imports;

use App\SettingApproval;
// use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Http\Traits\LoggedUser;

class SettingApprovalImport implements ToCollection, WithStartRow, WithValidation
{
    public function collection(Collection $rows)
    {

        foreach ($rows as $row) 
        {
            SettingApproval::create([
               'nama_requester'        => $row[1],
               'id_karyawan_requester' => $row[2],
               'nama_approver'         => $row[3],
               'id_karyawan_approver'  => $row[4],
               'created_by'            => LoggedUser::get()['user']->full_name,
            ]);
        }
    }

    public function startRow(): int
    {
        return 3;
    }

    public function rules(): array
    {
        return [
            '*.1' => ['exists:karyawan,nama'],
            '*.2' => ['exists:karyawan,id_karyawan'],
            '*.3' => ['exists:karyawan,nama'],
            '*.4' => ['exists:karyawan,id_karyawan'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            '1.exists' => 'Nama Karyawan Requestor Is Not Exists',
            '2.exists' => 'Id Karyawan Requestor Is Not Exists',
            '3.exists' => 'Nama Karyawan Approval Is Not Exists',
            '4.exists' => 'Id Karyawan Approval Is Not Exists',
        ];
    }
}