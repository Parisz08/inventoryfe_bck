<table class="table align-items-center mb-0" style="text-align:center;">
  <thead style="text-align:center;">
    <tr>
        <th colspan="33" style="background-color: #B0C4DE; font-size: 13px; text-align: center; font-weight: bold;">
            PAYROLL PT. BUANA CENTRA KARYA - PERIODE {{ strtoupper(\Carbon\Carbon::parse($peroideStart)->formatLocalized('%d %B')) }} s/d {{ strtoupper(\Carbon\Carbon::parse($peroideEnd)->formatLocalized('%d %B %Y')) }}
        </th>
    </tr>
    <tr>
      <th colspan="10" style="background-color: #6495ED; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder text-center">BASIC DATA</th>
      <th rowspan="2" style="background-color: #BDB76B; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder align-middle">ALPA</th>
      <th rowspan="2" style="background-color: #BDB76B; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder align-middle">HK</th>
      <th rowspan="2" style="background-color: #BDB76B; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder align-middle">OT</th>
      <th rowspan="2" style="background-color: #BDB76B; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder align-middle">LEMBUR</th>
      <th rowspan="2" style="background-color: #BDB76B; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder align-middle">BONUS</th>
      <th rowspan="2" style="background-color: #BDB76B; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder align-middle">TOTAL GAJI</th>
      <th colspan="11" style="background-color: gray; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder text-center">POTONGAN</th>
      <th colspan="6" style="background-color: #00CED1; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder text-center">FINAL SALARY</th>
    </tr>
    <tr style="color: white;">
      <th style="background-color: #FF7F50; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder">ID KARYAWAN</th>
      <th style="background-color: #FF7F50; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder">NAMA KARYAWAN</th>
      <th style="background-color: #FF7F50; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">UNIT</th>
      <th style="background-color: #FF7F50; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">STATUS</th>
      <th style="background-color: #FF7F50; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">HARIAN</th>
      <th style="background-color: #FF7F50; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">BULANAN</th>
      <th style="background-color: #FF7F50; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">GAJI POKOK</th>
      <th style="background-color: #FF7F50; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">TJ SKILL/JABATAN</th>
      <th style="background-color: #FF7F50; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">TRANSPORT</th>
      <th style="background-color: #FF7F50; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">MAKAN</th>
      <th style="background-color: #6495ED; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">PIUTANG</th>
      <th style="background-color: #6495ED; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">PINJAMAN</th>
      <th style="background-color: #6495ED; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">PPH21</th>
      <th style="background-color: #6495ED; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">Upah BPJS</th>
      <th style="background-color: #6495ED; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">JHT</th>
      <th style="background-color: #6495ED; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">JKM</th>
      <th style="background-color: #6495ED; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">JKK</th>
      <th style="background-color: #6495ED; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">JP</th>
      <th style="background-color: #6495ED; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">JKS</th>
      <th style="background-color: #6495ED; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">TOTAL POTONGAN BPJS</th>
      <th style="background-color: #6495ED; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">TOTAL POTONGAN</th>
      <th style="background-color: #90EE90; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">GAJI DITERIMA</th>
      <th style="background-color: #90EE90; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">SUDAH DI TF</th>
      <th style="background-color: #90EE90; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">KEKURANGAN</th>
      <th style="background-color: #90EE90; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">NO REK</th>
      <th style="background-color: #90EE90; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">AN REK</th>
      <th style="background-color: #90EE90; font-size: 11px; text-align: center; font-weight: bold;" class="text-uppercase text-xxs font-weight-bolder ">BANK</th>
    </tr>
  </thead>
  <tbody>
    @foreach($data as $index => $value)
    <tr>
      <td style="text-align: center;">{{ $value->id_karyawan }}</td>
      <td style="text-align: left;">{{ $value->nama }}</td>
      <td style="text-align: left;">{{ $value->unit }}</td>
      <td style="text-align: center;">{{ $value->status }}</td>
      <td style="text-align: center;">{{ number_format($value->harian,1,',','.') }}</td>
      <td style="text-align: center;">{{ number_format($value->bulanan,1,',','.') }}</td>
      <td style="text-align: center;">
        {{ number_format((($value->harian == 0) ? (($value->bulanan / 22) * $value->total_kerja_count) : ($value->harian * $value->total_kerja_count)),1,',','.') }}
      </td>
      <td style="text-align: center;">{{ number_format($value->tj_jabatan_skill,1,',','.') }}</td>
      <td style="text-align: center;">{{ number_format($value->transport,1,',','.') }}</td>
      <td style="text-align: center;">{{ number_format(($value->makan * $value->total_kerja_count),1,',','.') }}</td>
      <td style="text-align: center;">{{ $value->total_alpa_count }}</td>
      <td style="text-align: center;">{{ $value->total_kerja_count }}</td>
      <td style="text-align: center;">{{ $value->total_ot_count }}</td>
      <td style="text-align: center;">{{ number_format($value->total_ot_count * (($value->unit == 'Head Quarter') ? 250000 : 22619),1,',','.') }}</td>
      <td style="text-align: center;"></td>
      <td style="text-align: center;">
        {{ number_format(((($value->harian == 0) ? (($value->bulanan / 22) * $value->total_kerja_count) : ($value->harian * $value->total_kerja_count)) 
           + $value->tj_jabatan_skill 
           + $value->transport 
           + ($value->makan * $value->total_kerja_count) 
           + $value->total_ot_count * (($value->unit == 'Head Quarter') ? 250000 : 22619)),1,',','.') }}
      </td>
      <td style="text-align: center;">{{ number_format($value->relPayroll->piutang,1,',','.') }}</td>
      <td style="text-align: center;">{{ number_format($value->relPayroll->pinjaman,1,',','.') }}</td>
      <td style="text-align: center;">PPH 21</td>
      <td style="text-align: center;">{{ number_format($value->upah_bpjs,1,',','.') }}</td>
      <td style="text-align: center;">{{ number_format($value->upah_bpjs * $value->jht,1,',','.') }}</td>
      <td style="text-align: center;">{{ number_format($value->upah_bpjs * $value->jkm,1,',','.') }}</td>
      <td style="text-align: center;">{{ number_format($value->upah_bpjs * $value->jkk,1,',','.') }}</td>
      <td style="text-align: center;">{{ number_format($value->upah_bpjs * $value->jp,1,',','.') }}</td>
      <td style="text-align: center;">{{ number_format($value->jks * 4309772,1,',','.') }}</td>
      <td style="text-align: center;">
          {{ number_format($value->upah_bpjs * $value->jht +  $value->upah_bpjs * $value->jkm + $value->upah_bpjs * $value->jkk + $value->upah_bpjs * $value->jp + $value->jks * 4309772,1,',','.') }}
      </td>
      <td style="text-align: center;">
          {{ number_format(($value->relPayroll->piutang 
          + $value->relPayroll->pinjaman 
          + ($value->upah_bpjs * $value->jht) 
          + ($value->upah_bpjs * $value->jkm) 
          + ($value->upah_bpjs * $value->jkk) 
          + ($value->upah_bpjs * $value->jp) 
          + ($value->jks * 4309772)),1,',','.') }}
      </td>
      <td style="text-align: center;">
          {{ number_format(
            ((($value->harian == 0) ? ($value->bulanan / 22 * $value->total_kerja_count) : ($value->harian * $value->total_kerja_count))
            + $value->tj_jabatan_skill 
            + $value->transport 
            + ($value->makan * $value->total_kerja_count)
            + ($value->total_ot_count * (($value->unit == 'Head Quarter') ? 250000 : 22619)))
            -
            ($value->upah_bpjs * $value->jht 
            + $value->upah_bpjs * $value->jkm 
            + $value->upah_bpjs * $value->jkk 
            + $value->upah_bpjs * $value->jp 
            + $value->jks * 4309772 
            + $value->relPayroll->piutang 
            + $value->relPayroll->pinjaman)
             ,0,',','.') }}
      </td>
      @if($value->relPayroll->status_tf == 'true')
        <td style="text-align: center; background-color: #00CED1;">SUDAH</td>
      @else
        <td style="text-align: center; background-color: #FF0000;">BELUM</td>
      @endif
      <td style="text-align: center;">{{ number_format($value->relPayroll->kekurangan,0,',','.') }}</td>
      <td style="text-align: center;">{{ $value->no_rek }}</td>
      <td style="text-align: center;">{{ $value->an_rek }}</td>
      <td style="text-align: center;">{{ $value->bank }}</td>
    </tr>
    @endforeach
    <tr>
  </tbody>
</table>