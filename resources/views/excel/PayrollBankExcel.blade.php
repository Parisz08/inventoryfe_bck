<table class="table align-items-center mb-0">
  <thead>
    <tr>
      @if($nama_bank == 'BANK BSI')
        <th colspan="7" style="background-color: #B0C4DE; font-size: 13px; text-align: center; font-weight: bold;">
            DATA PAYROLL {{$nama_bank}}
        </th>
      @elseif($nama_bank == 'BANK MANDIRI')
        <th colspan="6" style="background-color: #B0C4DE; font-size: 13px; text-align: center; font-weight: bold;">
            DATA PAYROLL {{$nama_bank}}
        </th>
      @else
        <th colspan="8" style="background-color: #B0C4DE; font-size: 13px; text-align: center; font-weight: bold;">
            DATA PAYROLL {{$nama_bank}}
        </th>
      @endif
    </tr>
    <tr style="color: white;">
      <th style="background-color: #ADFF2F; text-align: center; font-weight: bold;">NO</th>
      <th style="background-color: #ADFF2F; text-align: center; font-weight: bold;">NAMA</th>
      <th style="background-color: #ADFF2F; text-align: center; font-weight: bold;">JABATAN</th>
      <th style="background-color: #ADFF2F; text-align: center; font-weight: bold;">GAJI</th>
      @if($nama_bank == 'BANK BSI' || $nama_bank == 'BANK LAIN')
        <th style="background-color: #ADFF2F; text-align: center; font-weight: bold;">ADM BANK</th>
      @endif
      @if($nama_bank == 'BANK LAIN')
        <th style="background-color: #ADFF2F; text-align: center; font-weight: bold;">GAJI DITERIMA</th>
      @endif
      <th style="background-color: #ADFF2F; text-align: center; font-weight: bold;">NO REKENING</th>
      <th style="background-color: #ADFF2F; text-align: center; font-weight: bold;">ATAS NAMA</th>
    </tr>
  </thead>
  <tbody>
    @foreach($data as $index => $value)
    <tr>
      <td style="text-align: center;">{{ $index + 1  }}</td>
      <td>{{ $value->nama }}</td>
      <td>{{ $value->jabatan }}</td>
      <td style="text-align: center;">
          Rp. {{ 
            number_format(((($value->harian == 0) ? ($value->bulanan / $value->relPayroll->periode_total_hk * $value->total_kerja_count) : ($value->harian * $value->total_kerja_count))
            + $value->tj_jabatan_skill 
            + $value->transport 
            + ($value->makan)
            + ($value->total_ot_count * (($value->unit == 'Head Quarter') ? 250000 : 22619)))
            -
            ($value->jht 
            + $value->jkm 
            + $value->jkk 
            + $value->jp 
            + $value->jks
            + $value->relPayroll->piutang 
            + $value->relPayroll->pinjaman),0,',','.')
              }}
      </td>
      @if($nama_bank == 'BANK BSI')
        <td style="text-align: center;"> Rp. 1.000 </td>
      @elseif($nama_bank == 'BANK LAIN')
        <td style="text-align: center;"> Rp. 6.500 </td>
        <td style="text-align: center;">
          Rp. {{ 
            number_format(((($value->harian == 0) ? ($value->bulanan / $value->relPayroll->periode_total_hk * $value->total_kerja_count) : ($value->harian * $value->total_kerja_count))
            + $value->tj_jabatan_skill 
            + $value->transport 
            + ($value->makan)
            + ($value->total_ot_count * (($value->unit == 'Head Quarter') ? 250000 : 22619)))
            -
            ($value->jht 
            + $value->jkm 
            + $value->jkk 
            + $value->jp 
            + $value->jks
            + $value->relPayroll->piutang 
            + $value->relPayroll->pinjaman) - 6500,0,',','.')
              }}
        </td>
      @endif
      <td style="text-align: center;">{{ $value->no_rek }}</td>
      <td>{{ $value->an_rek }}</td>
    </tr>
    @endforeach
    <tr>
      <td colspan="3" style="text-align: center; font-weight: bold;">TOTAL</td>
      <td style="text-align: center; font-weight: bold;">Rp. {{ number_format($totalGaji,0,',','.') }}</td>
      @if($nama_bank == 'BANK BSI')
        <td style="text-align: center; font-weight: bold;">Rp. {{ number_format( (1000 * count($data)) ,0,',','.') }}</td>
      @elseif($nama_bank == 'BANK LAIN')
        <td style="text-align: center; font-weight: bold;">Rp. {{ number_format( (6500 * count($data)) ,0,',','.') }}</td>
        <td style="text-align: center; font-weight: bold;">Rp. {{ number_format($totalGaji - (6500 * count($data)),0,',','.') }}</td>
      @endif
      
    </tr>
    <tr>
  </tbody>
</table>