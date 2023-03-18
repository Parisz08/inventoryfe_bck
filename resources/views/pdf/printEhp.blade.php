<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>

	<!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.5.1/chart.min.js"></script>
</head>
<body>
	<!-- <p>Hallo ini adalah notifikasi slip gaji PT. Buana Centra Karya bulan November 2022 atas nama $nama, apabila ada pertanyaan lebih lanjut silahkan hubungi ke nomor ini 089696925665</p>

	<img align="center" alt="" src="$img_slip" width="564"> -->

  <div class="py-4 container-fluid">
    <div class=" row">
      <div class="col-12">
          	<!-- ============ HEADER =========== -->
            <div class="mt-5 mb-3">
			        <div class="row">
			          <div class="col-3">
			            <img src="{{ URL('bck.png') }}" style="width: 90px; margin-left: 20px; margin-top: -50px;"/>
			          </div>
			          <div class="col-6" style="margin-left: -120px; margin-top: 10px; margin-top: -40px;">
			            <h5>PT. BUANA CENTRA KARYA</h5>
			            <h5>PIPE MANUFACTURING & STEEL FABRICATION</h5>
			          </div>
			          <div class="col-1">
			            <div style="margin-left: -30px; margin-top: -50px;" class="vl"></div>
			          </div>
			          <div class="col-2">
			            <h4 style="margin-left: -90px; margin-top: 10PX; margin-top: -40px;">EMPLOYEE HOUR PERFORMANCE</h4>
			          </div>
			        </div>
			      </div><hr width="940px">

			      <!-- ============ BODY =========== -->
            <div class="container">
              <div class="mt-3">
              	<p>Periode Start &nbsp;: <?php setlocale(LC_ALL, 'IND'); ?> {{ \Carbon\Carbon::parse($peroideStart)->formatLocalized('%d %B %Y') }} </p>
              	<p style="margin-top: -13px;">Periode End &ensp;: {{ \Carbon\Carbon::parse($peroideEnd)->formatLocalized('%d %B %Y') }} </p>
              	<table class="table table-bordered align-items-center mb-5">
			            <thead>
			              <tr style="background-color: #F0F8FF;">
			                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">NAMA</th>
			                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">UNIT</th>
			                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">JABATAN</th>
			                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">JAM KERJA</th>
			                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">JAM LEMBUR</th>
			                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">TOTAL JAM</th>
			              </tr>
			            </thead>
			            <tbody>
			            	@foreach($data as $index => $value)
			              <tr>
			                <td class="align-middle text-center text-sm">
			                  <span class="text-secondary text-xs font-weight-bold">  {{$value->nama}}  </span>
			                </td>
			                <td class="align-middle text-center">
			                  <span class="text-secondary text-xs font-weight-bold">  {{$value->unit}}  </span>
			                </td>
			                <td class="align-middle text-center">
			                  <span class="text-secondary text-xs font-weight-bold">  {{$value->jabatan}}  </span>
			                </td>
			                <td class="align-middle text-center">
			                  <span class="text-secondary text-xs font-weight-bold">  {{ (8 * $value->total_kerja_count) }}  </span>
			                </td>
			                <td class="align-middle text-center">
			                  <span class="text-secondary text-xs font-weight-bold">  {{ $value->total_ot_count }}  </span>
			                </td>
			                <td class="align-middle text-center">
			                  <span class="text-secondary text-xs font-weight-bold">  {{ (+(8 * $value->total_kerja_count) + +($value->total_ot_count)) }}  </span>
			                </td>
			              </tr>
			              @endforeach
			            </tbody>
			          </table>
            </div>
          </div>

    </div>
  </div>
<script>
  setTimeout(function () {
      window.print();
    },1000); // 5 seconds
</script>

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

.vl {
  border-left: 3px solid ;
  height: 70px;
}
</style>

</body>
</html>