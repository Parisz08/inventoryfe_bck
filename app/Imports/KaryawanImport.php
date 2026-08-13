<?php

namespace App\Imports;

use App\Karyawan;
// use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Http\Traits\LoggedUser;

class KaryawanImport implements ToCollection, WithStartRow, WithValidation
{
    public function collection(Collection $rows)
    {

        foreach ($rows as $row) 
        {
            // SEQUENCE
            $lastSeq = DB::table('karyawan')->pluck('id_karyawan')->last();
            if (empty($lastSeq)) {
                $seq = str_pad(1, 3, '0', STR_PAD_LEFT);
            }else{
                $sum = substr($lastSeq, -3) + 1;
                $seq = str_pad($sum, 3, '0', STR_PAD_LEFT);
            }
            $karyawanId = 'BCK-'.$seq;

            Karyawan::create([
               'id_karyawan'      => $row[1],
               'nama'             => $row[2],
               'jabatan'          => $row[3],
               'unit'             => $row[4],
               'harian'           => ($row[5] == null) ? 0 : $row[5],
               'bulanan'          => $row[6],
               'tj_jabatan_skill' => $row[7],
               'transport'        => $row[8],
               'makan'            => $row[9],
               'bank'             => $row[10],
               'no_rek'           => $row[11],
               'an_rek'           => $row[12],
               'no_bpjs_tk'       => $row[13],
               'no_bpjs_kes'      => $row[14],
               'upah_bpjs'        => $row[15],
               'jht'              => $row[16],
               'jkm'              => $row[17],
               'jkk'              => $row[18],
               'jp'               => $row[19],
               'jks'              => $row[20],
               'nik'              => $row[21],
               'no_hp'            => $row[22],
               'email'            => $row[23],
               'status'           => 'Aktif',
               'created_by'       => LoggedUser::get()['user']->full_name,
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
            '*.2'  => ['required'],
            '*.3'  => ['required'],
            '*.4'  => ['required'],
            '*.10' => ['required'],
            '*.11' => ['required'],
            '*.12' => ['required'],
            // '*.21' => ['required'],
            // '*.22' => ['required'],
            // '*.23' => ['required'],
            // '*.5' => ['exists:client_master,client_name'],
            // '*.6' => ['unique:mother_coil,coil_no'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            '2.required' => 'Nama Karyawan Is Required',
            '3.exists'   => 'Jabatan Is Required',
            '4.unique'   => 'Unit Is Required',
            '10.unique'  => 'Nama Bank Is Required',
            '11.unique'  => 'No Rekening Is Required',
            '12.unique'  => 'An Rekening Is Required',
            // '21.unique'  => 'NIK Is Required',
            // '22.unique'  => 'NO HP Is Required',
            // '23.unique'  => 'Email Is Required',
        ];
    }
}