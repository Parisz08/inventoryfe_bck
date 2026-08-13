<?php

namespace App\Imports;

use App\User;
// use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;

class UsersImport implements ToCollection, WithStartRow, WithValidation
{
    public function collection(Collection $rows)
    {

        foreach ($rows as $row) 
        {
            User::create([
               'employee_id' => $row[1],
               'full_name'   => $row[2],
               'username'    => $row[3],
               'password'    => password_hash($row[4], PASSWORD_BCRYPT),
               'role'        => $row[5],
               'status'      => 'Aktif',
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
            '*.1' => ['required'],
            '*.2' => ['required'],
            '*.3' => ['unique:users,username'],
            '*.4' => ['required'],
            '*.5' => ['required'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            '1.required' => 'Full Name Is Required',
            '2.required' => 'Full Name Is Required',
            '3.required' => 'Username Is Unique',
            '4.required' => 'Password Is Required',
            '5.required' => 'Role Is Required',
        ];
    }
}