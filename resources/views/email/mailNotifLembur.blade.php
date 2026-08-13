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
									      <p style="font-size: 20px; font-weight: bold;">SURAT PERINTAH LEMBUR </p>
									    </td>
									  </tr>
									</table>
                </div>
              </div>
            </div>
            <div class="container">
              <div class="container mt-3">
              	<p>Dengan ini di perintahkan untuk melaksanakan pekerjaan lembur di luar hari/jam kerja</p>
				        <p>Code SPL &ensp;&nbsp;&ensp;&nbsp;&ensp;&ensp;&ensp;&nbsp;&nbsp;: SHGSHJDDG</p>
				        <p style="margin-top: -13px;">
				          Tanggal &ensp;&nbsp;&ensp;&nbsp;&ensp;&nbsp;&ensp;&nbsp; &ensp;&ensp;: 12/02/2023
				        </p>
				        <p style="margin-top: -13px;">
				          Total Aktual Jam &ensp; : 8
				        </p>
				        <p style="margin-top: -13px;">
				          Uraian Pekerjaan &nbsp;&nbsp;:
				          <textarea style="border: 1px dotted dimgray; border-radius: 15px 15px 15px 15px;; resize: none; width: 100%; margin-top: 10px;" placeholder="Masukkan Uraian Pekerjaan ...." rows="5">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
				          tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
				          quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
				          consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
				          cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
				          proident, sunt in culpa qui officia deserunt mollit anim id est laborum.	</textarea>
				        </p>
              	
              	<p>Dengan Anggota Sebagai Berikut :</p>
              	<table class="table align-items-center mb-5">
			            <thead>
			              <tr style="background-color: #F0F8FF;">
			                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">NAMA</th>
			                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">POSISI</th>
			                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">UNIT</th>
			                <th v-if="tableAnggotaToTableSPL.data.length !== 0"></th>
			              </tr>
			            </thead>
			            <tbody>
			              <tr v-for="(row, i) in tableAnggotaToTableSPL.data" :key="i">
			                <td class="align-middle text-center text-sm">
			                  <span class="text-secondary text-xs font-weight-bold">  row.nama  </span>
			                </td>
			                <td class="align-middle text-center">
			                  <span class="text-secondary text-xs font-weight-bold">  row.posisi  </span>
			                </td>
			                <td class="align-middle text-center">
			                  <span class="text-secondary text-xs font-weight-bold">  row.unit  </span>
			                </td>
			                <td style="text-align: center;" v-if="row.created_by == roleFullName">
			                  <i class="fa fa-times-circle fa-lg" aria-hidden="true" title="Delete" style="cursor: pointer;" @click="removeAnggota(row.id)"></i>
			                </td>
			              </tr>
			            </tbody>
			          </table>
                 
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