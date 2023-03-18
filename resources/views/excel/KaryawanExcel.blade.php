<table class="table align-items-center mb-0">
  <thead>
    <tr>
        <th colspan="25" style="background-color: #B0C4DE; font-size: 13px; text-align: center; font-weight: bold;">
            DATA KARYAWAN
        </th>
    </tr>
    <tr style="color: white;">
      <th style="background-color: #ADFF2F; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder">NO</th>
      <th style="background-color: #ADFF2F; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder">ID KARYAWAN</th>
      <th style="background-color: #ADFF2F; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder">NAMA KARYAWAN</th>
      <th style="background-color: #ADFF2F; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">JABATAN</th>
      <th style="background-color: #ADFF2F; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">UNIT</th>
      <th style="background-color: #ADFF2F; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">STATUS</th>
      <th style="background-color: #ADFF2F; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">HARIAN</th>
      <th style="background-color: #ADFF2F; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">BULANAN</th>
      <th style="background-color: #ADFF2F; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">TJ SKILL/JABATAN</th>
      <th style="background-color: #ADFF2F; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">TRANSPORT</th>
      <th style="background-color: #ADFF2F; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">MAKAN</th>
      <th style="background-color: #ADFF2F; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">NAMA BANK</th>
      <th style="background-color: #ADFF2F; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">NO REKENING</th>
      <th style="background-color: #ADFF2F; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">AN REKENING</th>
      <th style="background-color: #ADFF2F; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">NO BPJS TK</th>
      <th style="background-color: #ADFF2F; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">NO BPJS KES</th>
      <th style="background-color: #ADFF2F; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">UPAH BPJS</th>
      <th style="background-color: #ADFF2F; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">JHT</th>
      <th style="background-color: #ADFF2F; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">JKM</th>
      <th style="background-color: #ADFF2F; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">JKK</th>
      <th style="background-color: #ADFF2F; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">JP</th>
      <th style="background-color: #ADFF2F; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">JKS</th>
      <th style="background-color: #ADFF2F; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">NIK</th>
      <th style="background-color: #ADFF2F; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">NO HP</th>
      <th style="background-color: #ADFF2F; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">EMAIL</th>
    </tr>
  </thead>
  <tbody>
    @foreach($data as $index => $value)
    <tr>
      <td style="text-align: center;">{{ $index + 1 }}</td>
      <td style="text-align: center;">{{ $value->id_karyawan }}</td>
      <td style="text-align: left;">{{ $value->nama }}</td>
      <td style="text-align: left;">{{ $value->jabatan }}</td>
      <td style="text-align: left;">{{ $value->unit }}</td>
      <td style="text-align: center;">{{ $value->status }}</td>
      <td style="text-align: center;">{{ number_format($value->harian,1,',','.') }}</td>
      <td style="text-align: center;">{{ number_format($value->bulanan,0,',','.') }}</td>
      <td style="text-align: center;">{{ number_format($value->tj_jabatan_skill,1,',','.') }}</td>
      <td style="text-align: center;">{{ number_format($value->transport,0,',','.') }}</td>
      <td style="text-align: center;">{{ number_format($value->makan,1,',','.') }}</td>
      <td style="text-align: center;">{{ $value->bank }}</td>
      <td style="text-align: center;">{{ $value->no_rek }}</td>
      <td style="text-align: center;">{{ $value->an_rek }}</td>
      <td style="text-align: center;">{{ $value->no_bpjs_tk }}</td>
      <td style="text-align: center;">{{ $value->no_bpjs_kes }}</td>
      <td style="text-align: center;">{{ number_format($value->upah_bpjs,0,',','.') }}</td>
      <td style="text-align: center;">{{ number_format($value->jht,0,',','.') }}</td>
      <td style="text-align: center;">{{ number_format($value->jkm,0,',','.') }}</td>
      <td style="text-align: center;">{{ number_format($value->jkk,0,',','.') }}</td>
      <td style="text-align: center;">{{ number_format($value->jp,0,',','.') }}</td>
      <td style="text-align: center;">{{ number_format($value->jks,0,',','.') }}</td>
      <td style="text-align: center;">{{ $value->nik }}</td>
      <td style="text-align: center;">{{ $value->no_hp }}</td>
      <td style="text-align: center;">{{ $value->email }}</td>
    </tr>
    @endforeach
    <tr>
  </tbody>
</table>