<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>
</head>
<body>
	<!-- <p>Hallo ini adalah notifikasi slip gaji PT. Buana Centra Karya bulan November 2022 atas nama $nama, apabila ada pertanyaan lebih lanjut silahkan hubungi ke nomor ini 089696925665</p>

	<img align="center" alt="" src="$img_slip" width="564"> -->

  <div class="py-4 container-fluid">
    <div class=" row">
      <div class="col-12">
          <div class="card"> 
            <div class="row">
              <div class="col-12">
                <div class="card-header pb-0 text-center" style="text-align:left; vertical-align:top;">

                	<!-- TABEL HEADER -->
                	<table width="100%;" style="border-collapse: seperate; border-spacing: 10px;">
									  <tr>
									    <td width="100%;"></td>
									  </tr>
									  <tr height="10px">
									    <td colspan="20" style="text-align:center; vertical-align:top;">
									      <p style="font-size: 20px;">SLIP GAJI -  {{ $periode }} </p><hr>
									    </td>
									  </tr>
									</table>
                </div>
              </div>
            </div>
            <div class="container">
              <div class="container">

              	<!-- TABEL DATA KARYAWAN -->
              	<table width="100%;" style="border-collapse: seperate; border-spacing: 10px;">
								  <tr>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								  </tr>
								  <tr height="150px">
								    <td colspan="10" style="text-align:left; vertical-align:top;">
								      <div class="">
		                    <p style="margin-top: 10px; font-size: 15px;">ID <span style="margin-left: 75px; margin-right: 20px;">:</span>  {{ $data->id_karyawan }} </p>
		                    <p style="margin-top: -10px; font-size: 15px;">NAMA <span style="margin-left: 47px; margin-right: 20px;">:</span> {{ $data->nama }} </p>
		                    <p style="margin-top: -10px; font-size: 15px;">JABATAN <span style="margin-left: 23px; margin-right: 20px;">:</span> {{ $data->jabatan }} </p>
		                    <p style="margin-top: -10px; font-size: 15px;">STATUS <span style="margin-left: 30px; margin-right: 20px;">:</span> {{ $data->status }} </p>
		                  </div>
								    </td>
								    <td colspan="10" style="text-align:center; vertical-align:top;">
                    	<img src="https://payroll.bck.co.id/img/bck.png" width="120" />
                    	<p>PT. BUANA CENTRA KARYA</p>
								    </td>
								  </tr>
								</table>
              	
								<!-- TABEL DETAIL SLIP -->
				        <table width="100%;" style="border-collapse: seperate; border-spacing: 10px;">
								  <tr>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								  </tr>
								  <tr height="150px">
								    <td colspan="10" style="text-align:left; vertical-align:top;">
								    	<div class="">
				                <hr>
				                PENERIMAAN
				                <hr style="margin-top: 2px;">
				                <p style="font-size: 15px;">Gaji Pokok <span style="margin-left: 66px; margin-right: 10px;">:</span> 
				                   {{ number_format(($data->harian == 0) ? (($data->bulanan / $relPay['periode_total_hk']) * $data->total_kerja_count) : ($data->harian * $data->total_kerja_count),0,',','.') }}
				                </p>
				                <p style="margin-top: -10px; font-size: 15px;">Tj. Jabatan / Skill <span style="margin-left: 23px; margin-right: 10px;">:</span>
				                   {{ number_format($data->tj_jabatan_skill,0,',','.') }}
				                </p>
				                <p style="margin-top: -10px; font-size: 15px;">Transport <span style="margin-left: 73PX; margin-right: 10px;">:</span> 
				                   {{ number_format($data->transport,0,',','.') }}
				                </p>
				                <p style="margin-top: -10px; font-size: 15px;">U. Makan <span style="margin-left: 72PX; margin-right: 10px;">:</span> 
				                   {{ number_format(($data->makan * $data->total_kerja_count),0,',','.') }} 
				                </p>
				                <p style="margin-top: -10px; font-size: 15px;">Lembur <span style="margin-left: 84PX; margin-right: 10px;">:</span> 
				                   {{ number_format($data->total_ot_count * (($data->unit == 'Head Quarter') ? 250000 : 22619),0,',','.') }}
				                </p>
				                <p style="margin-top: -10px; font-size: 15px;">Jam Lembur <span style="margin-left: 52PX; margin-right: 10px;">:</span> 
				                   {{ number_format($data->total_ot_count,0,',','.') }} 
				                </p>
				                <p style="margin-top: -10px; font-size: 15px;">Bonus <span style="margin-left: 92PX; margin-right: 10px;">:</span> -</p>
				              </div>
								    </td>
								    <td colspan="10" style="text-align:left; vertical-align:top;">
										<div class="">
											<hr>
											POTONGAN
											<hr style="margin-top: 2px;">
											<p style="font-size: 15px;">JHT <span style="margin-left: 105px; margin-right: 10px;">:</span> 
											   {{ number_format($data->upah_bpjs * $data->jht,0,',','.') }} 
											</p>
											<p style="margin-top: -10px; font-size: 15px;">JKM <span style="margin-left: 102px; margin-right: 10px;">:</span> 
											   {{ number_format($data->upah_bpjs * $data->jkm,0,',','.') }}  
											</p>
											<p style="margin-top: -10px; font-size: 15px;">JKK <span style="margin-left: 106px; margin-right: 10px;">:</span> 
											   {{ number_format($data->upah_bpjs * $data->jkk,0,',','.') }}  
											</p>
											<p style="margin-top: -10px; font-size: 15px;">JP <span style="margin-left: 115px; margin-right: 10px;">:</span> 
											   {{ number_format($data->upah_bpjs * $data->jp,0,',','.') }}  
											</p>
											<p style="margin-top: -10px; font-size: 15px;">JKS <span style="margin-left: 105px; margin-right: 10px;">:</span> 
											   {{ number_format($data->jks * 4309772,0,',','.') }}  
											</p>
											<p style="margin-top: -10px; font-size: 15px;">PPH21 <span style="margin-left: 85px; margin-right: 10px;">:</span> -</p>
											<p style="margin-top: -10px; font-size: 15px;">Lainnya <span style="margin-left: 77px; margin-right: 10px;">:</span> 
												@if($relPay != null)
											      {{ number_format(($relPay['piutang']) + ($relPay['pinjaman']),0,',','.') }}
											    @else
											      - 
												@endif
											</p>
										</div>
								    </td>
								  </tr>
								</table>
                <hr style="margin-top: 0px;">

                <!-- TABEL TOTAL KALKULASI -->
                <table width="100%;" style="border-collapse: seperate; border-spacing: 10px;">
								  <tr>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								    <td width="5%;"></td>
								  </tr>
								  <tr height="20px">
								    <td colspan="10" style="text-align:left; vertical-align:top;">
								      <div style="margin-top: -10px; font-size: 18px; font-weight: bold;">
		                    TOTAL <span style="margin-left: 150px; margin-right: 30px;"></span>  
		                    {{ number_format(
		                    (($data->harian == 0) ? ($data->bulanan / $relPay['periode_total_hk'] * $data->total_kerja_count) : ($data->harian * $data->total_kerja_count))
		                    + $data->tj_jabatan_skill 
		                    + $data->transport 
		                    + ($data->makan * $data->total_kerja_count)
		                    + ($data->total_ot_count * (($data->unit == 'Head Quarter') ? 250000 : 22619))
		                    ,0,',','.') }}
		                    <hr style="margin-top: 2px;">
		                  </div>
								    </td>
								    <td colspan="10" style="text-align:left; vertical-align:top;">
								    	<div style="margin-top: -10px; font-size: 18px; font-weight: bold;">
								    		@if($relPay != null)
		                      TOTAL <span style="margin-left: 150px; margin-right: 30px;"></span>  {{ number_format(
		                      $data->upah_bpjs * $data->jht 
		                      + $data->upah_bpjs * $data->jkm 
		                      + $data->upah_bpjs * $data->jkk 
		                      + $data->upah_bpjs * $data->jp 
		                      + $data->jks * 4309772 
		                      + $relPay['piutang'] 
		                      + $relPay['pinjaman']
		                      ,0,',','.') }}
										    @else
										      - 
											  @endif
				                   <hr style="margin-top: 2px;">
				                </div>
								    </td>
								  </tr>
								</table>
                  
                <!-- TABEL THP -->
                <table width="100%;" style="border-collapse: seperate; border-spacing: 10px;">
								  <tr>
								    <td width="100%;"></td>
								  </tr>
								  <tr height="10px">
								    <td colspan="20" style="text-align:center; vertical-align:top;">
								      @if($relPay != null)
	                      <p style="font-size: 18px; font-weight: bold;">THP <span style="margin-left: 65px; "></span> <span style="border-bottom: 3px double;">
	                         {{ number_format(
	                        ((($data->harian == 0) ? ($data->bulanan / $relPay['periode_total_hk'] * $data->total_kerja_count) : ($data->harian * $data->total_kerja_count))
		                    + $data->tj_jabatan_skill 
		                    + $data->transport 
		                    + ($data->makan * $data->total_kerja_count)
		                    + ($data->total_ot_count * (($data->unit == 'Head Quarter') ? 250000 : 22619)))
		                    -
		                    ($data->upah_bpjs * $data->jht 
		                    + $data->upah_bpjs * $data->jkm 
		                    + $data->upah_bpjs * $data->jkk 
		                    + $data->upah_bpjs * $data->jp 
		                    + $data->jks * 4309772 
		                    + $relPay['piutang'] 
		                    + $relPay['pinjaman'])
	                         ,0,',','.') }}
	                      </span></p>
	                    @else
									    	- 
									  	@endif
								    </td>
								  </tr>
								</table>

                <p style="margin-left: 58px; margin-top: 70px; margin-bottom: 80px;">Payroll</p>
                <span style="border-bottom: 3px double; margin-left: 35px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                <hr style="margin-top: 30px;">
                <p style="font-size: 13px; color: red;">Note : apabila ada pertanyaan lebih lanjut silahkan hubungi ke nomor ini 089696925665</p>
              </div>
              <div class="card-footer">
              </div>
            </div>
          </div>
      </div>
    </div>
  </div>


<style scoped>
::v-deep .modal-container {
  display: flex;
  justify-content: center;
  align-items: center;
  overflow-y: auto;

}
::v-deep .modal-content {
  display: flex;
  flex-direction: column;
  margin: 0 1rem;
  padding: 1rem;
  border: 1px solid #e2e8f0;
  border-radius: 0.25rem;
  background: #fff;
  width: 500px;
  margin-top: auto;
  margin-bottom: auto;
}
.modal__title {
  font-size: 1rem;
  font-weight: 600;
}
</style>

<style scoped>
.dark-mode div::v-deep .modal-content {
  border-color: #2d3748;
  background-color: #1a202c;
}
.scroll { 
  overflow: auto; 
  height: 500px; 
}
.scroll thead th { 
  position: sticky; 
  top: 0; 
  z-index: 100; 
  background-color: #F0F8FF;
}
</style>

</body>
</html>