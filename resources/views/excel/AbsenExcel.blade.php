<table class="table align-items-center mb-0">
  <thead>
    <tr>
      <?php 
        setlocale(LC_ALL, 'IND');
      ?>
      <th colspan="39" style="background-color: #B0C4DE; font-size: 13px; text-align: center; font-weight: bold;">
          ABSENSI PT. BUANA CENTRA KARYA - PERIODE {{ strtoupper(\Carbon\Carbon::parse($peroideStart)->formatLocalized('%d %B')) }} s/d {{ strtoupper(\Carbon\Carbon::parse($peroideEnd)->formatLocalized('%d %B %Y')) }}
      </th>
    </tr>
    <tr style="color: white;">
      <th class="text-uppercase text-xxs font-weight-bolder " style="background-color: #90EE90; font-size: 10px; text-align: center; font-weight: bold;">Nama Karyawan</th>
      <th style="background-color: #90EE90; font-size: 10px; text-align: center; font-weight: bold;"></th>
      @foreach($data as $index => $value)
      <?php 
        setlocale(LC_ALL, 'IND');
       ?>
        <th class="text-uppercase text-xxs font-weight-bolder ps-2 text-center" style="background-color: #90EE90; font-size: 10px; text-align: center; font-weight: bold;">
          <p class="text-xs font-weight-bold mb-0 text-center">{{ \Carbon\Carbon::parse($value->date)->formatLocalized('%A') }}</p>
          <p class="text-xs font-weight-bold mb-0 text-center">{{ \Carbon\Carbon::parse($value->date)->formatLocalized('%d') }}</p>
        </th>
      @endforeach
      <th class="text-uppercase text-xxs font-weight-bolder ps-2 text-center" style="background-color: #FF8C00; text-align: center;"><p class="text-xs font-weight-bold mb-0 text-center">S</p></th>
      <th class="text-uppercase text-xxs font-weight-bolder ps-2 text-center" style="background-color: #FF8C00; text-align: center;"><p class="text-xs font-weight-bold mb-0 text-center">I</p></th>
      <th class="text-uppercase text-xxs font-weight-bolder ps-2 text-center" style="background-color: #FF8C00; text-align: center;"><p class="text-xs font-weight-bold mb-0 text-center">A</p></th>
      <th class="text-uppercase text-xxs font-weight-bolder ps-2 text-center" style="background-color: #FF8C00; text-align: center;"><p class="text-xs font-weight-bold mb-0 text-center">C</p></th>
      <th class="text-uppercase text-xxs font-weight-bolder ps-2 text-center" style="background-color: #FF8C00; text-align: center;"><p class="text-xs font-weight-bold mb-0 text-center">HK</p></th>
      <th class="text-uppercase text-xxs font-weight-bolder ps-2 text-center" style="background-color: #FF8C00; text-align: center;"><p class="text-xs font-weight-bold mb-0 text-center">OT</p></th>
    </tr>
  </thead>
  <tbody>
    @foreach($absenKaryawan as $index => $row)
    <tr>
      <td>
        <div class="d-flex px-2 py-1">
          <div class="d-flex flex-column justify-content-center">
            <h6 class="mb-0 text-sm">{{ $row->nama }}</h6>
            <p class="text-xs text-secondary mb-0">{{ $row->unit }}</p>
          </div>
        </div>
      </td>
      <td>
        <p class="text-xs font-weight-bold mb-0 text-center" style="background-color: #FFFACD;">HK</p>
        <p class="text-xs font-weight-bold mb-0 text-center">OT</p>
      </td>
      @foreach($row->relAbsenKaryawan as $index => $i)
      <td style="text-align: center;">
        <p class="text-xs font-weight-bold mb-0 text-center">{{ $i->type_hk }}</p>
        <p class="text-xs font-weight-bold mb-0 text-center">{{ $i->type_ot }}</p>
      </td>
      @endforeach
      <td style="background-color: #8FBC8F; text-align: center;">{{ $row->total_sakit_count }}</td>
      <td style="background-color: #DCDCDC; text-align: center;">{{ $row->total_ijin_count }}</td>
      <td style="background-color: #FF6347; text-align: center;">{{ $row->total_alpa_count }}</td>
      <td style="background-color: yellow; text-align: center;">{{ $row->total_cuti_count }}</td>
      <td style="background-color: #FFFACD; text-align: center;">{{ $row->total_kerja_count }}</td>
      <td style="background-color: #FFD700; text-align: center;">{{ $row->total_ot_count }}</td>
    </tr>
    @endforeach
    <tr>
     
    </tr>
  </tbody>
</table>