<template>
  <div class="py-4 container-fluid">
    <a :style="search.periode_start == '' ? 'margin-right: 10px; pointer-events: none; cursor: default;' : 'margin-right: 10px;'" style="margin-right: 10px;" :href="apiUrl+'export-excel/payroll?periode_start='+search.periode_start+'&periode_end='+search.periode_end+'&nama='+search.nama+''" target="_BLANK"><argon-button :disabled="search.periode_start == ''" color="primary" size="sm" class="mb-3" variant="gradient"><i class="fa fa-file-excel-o" style="margin-right: 5px;"></i> Export Excel</argon-button></a>
    <argon-button color="warning" size="sm" class="mb-3" variant="gradient"><i class="fa fa-file-pdf-o" style="margin-right: 5px;"></i> Export PDF</argon-button>
    <div class=" row">
      <div class="col-12">
          <div class="card"> 
            <div class="row">
              <div class="col-2">
                <div class="card-header pb-0">
                  <h6>Data Payroll</h6>
                </div>
              </div>
              <div class="col-5">
                <p style="margin-top: 25px; font-weight: bold; padding-left: 85px;">PERIODE {{ moment(search.periode_start).locale('id').format('D MMMM').toUpperCase() }} - {{ moment(search.periode_end).locale('id').format('D MMMM YYYY').toUpperCase() }}</p>
              </div>
              <div class="col-5 float-right">
                <argon-button
                  style="margin-right: 10px; margin-left: 100px;"
                  class="mt-4"
                  variant="gradient"
                  color="secondary"
                  size="sm"
                  @click="filter()"
                ><i class="fa fa-filter fa-sm" aria-hidden="true" style="margin-right: 5px;"></i> Filter</argon-button>
                <argon-button
                  class="mt-4"
                  variant="gradient"
                  color="success"
                  size="sm"
                  @click="sendSlip()"
                  :disabled="onLoadingSendSlip == true || search.periode_start == ''"
                >
                  <span v-if="onLoadingSendSlip"><i class="fa fa-spinner fa-spin"></i> Please Wait...</span>
                  <span v-else>
                      <span><i class="fa fa-paper-plane fa-sm" style="margin-right: 5px;" aria-hidden="true"></i> Send Slip</span>
                  </span>
                </argon-button>
              </div>
            </div>
            
            <div class="card-body px-0 pt-0 pb-2 mt-4">
              <div class="table-responsive p-0 scroll">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr style="color: white;">
                      <th colspan="9" class="text-uppercase text-xxs font-weight-bolder text-center">Basic Data</th>
                      <th rowspan="2" style="background-color: #BDB76B;" class="text-uppercase text-xxs font-weight-bolder align-middle">Alpa</th>
                      <th rowspan="2" style="background-color: #BDB76B;" class="text-uppercase text-xxs font-weight-bolder align-middle">HK</th>
                      <th rowspan="2" style="background-color: #BDB76B;" class="text-uppercase text-xxs font-weight-bolder align-middle">OT</th>
                      <th rowspan="2" style="background-color: #BDB76B;" class="text-uppercase text-xxs font-weight-bolder align-middle">Lembur</th>
                      <th rowspan="2" style="background-color: #BDB76B;" class="text-uppercase text-xxs font-weight-bolder align-middle">Bonus</th>
                      <th rowspan="2" style="background-color: #BDB76B;" class="text-uppercase text-xxs font-weight-bolder align-middle">Total Gaji</th>
                      <th colspan="11" style="background-color: dimgrey;" class="text-uppercase text-xxs font-weight-bolder text-center">Potongan</th>
                      <th colspan="8" style="background-color: #00CED1;" class="text-uppercase text-xxs font-weight-bolder text-center">Final Salary</th>
                    </tr>
                    <tr style="color: white;">
                      <th style="background-color: #FF7F50;" class="text-uppercase text-xxs font-weight-bolder">Nama Karyawan</th>
                      <th style="background-color: #FF7F50;" class="text-uppercase text-xxs font-weight-bolder ">Unit</th>
                      <th style="background-color: #FF7F50;" class="text-uppercase text-xxs font-weight-bolder ">Status</th>
                      <th style="background-color: #FF7F50;" class="text-uppercase text-xxs font-weight-bolder ">Harian</th>
                      <th style="background-color: #FF7F50;" class="text-uppercase text-xxs font-weight-bolder ">Bulanan</th>
                      <th style="background-color: #FF7F50;" class="text-uppercase text-xxs font-weight-bolder ">Gaji Pokok</th>
                      <th style="background-color: #FF7F50;" class="text-uppercase text-xxs font-weight-bolder ">Tj Skill/Jabatan</th>
                      <th style="background-color: #FF7F50;" class="text-uppercase text-xxs font-weight-bolder ">Transport</th>
                      <th style="background-color: #FF7F50;" class="text-uppercase text-xxs font-weight-bolder ">Makan</th>
                      <th class="text-uppercase text-xxs font-weight-bolder ">Piutang</th>
                      <th class="text-uppercase text-xxs font-weight-bolder ">Pinjaman</th>
                      <th class="text-uppercase text-xxs font-weight-bolder ">PPH21</th>
                      <th class="text-uppercase text-xxs font-weight-bolder ">Upah BPJS</th>
                      <th class="text-uppercase text-xxs font-weight-bolder ">JHT</th>
                      <th class="text-uppercase text-xxs font-weight-bolder ">JKM</th>
                      <th class="text-uppercase text-xxs font-weight-bolder ">JKK</th>
                      <th class="text-uppercase text-xxs font-weight-bolder ">JP</th>
                      <th class="text-uppercase text-xxs font-weight-bolder ">JKS</th>
                      <th class="text-uppercase text-xxs font-weight-bolder ">TOTAL POTONGAN BPJS</th>
                      <th class="text-uppercase text-xxs font-weight-bolder ">TOTAL POTONGAN</th>
                      <th style="background-color: #90EE90;" class="text-uppercase text-xxs font-weight-bolder ">Gaji Diterima</th>
                      <th style="background-color: #90EE90;" class="text-uppercase text-xxs font-weight-bolder ">Sudah Di TF</th>
                      <th style="background-color: #90EE90;" class="text-uppercase text-xxs font-weight-bolder ">Kekurangan</th>
                      <th style="background-color: #90EE90;" class="text-uppercase text-xxs font-weight-bolder ">No Rek</th>
                      <th style="background-color: #90EE90;" class="text-uppercase text-xxs font-weight-bolder ">AN Rek</th>
                      <th style="background-color: #90EE90;" class="text-uppercase text-xxs font-weight-bolder ">Bank</th>
                      <th style="background-color: #90EE90;" class="text-uppercase text-xxs font-weight-bolder ">Slip Gaji</th>
                      <th style="background-color: #90EE90;" class="text-uppercase text-xxs font-weight-bolder ">Send Slip Gaji</th>
                      
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(row, i) in absenKaryawan.data" :key="i" @click="setBg(i)" :style="(backgroundRed == i) ? 'background-color: #F0F8FF; cursor: pointer;' : 'cursor: pointer;' ">
                      <td>
                        <div class="d-flex px-2 py-1">
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm">{{ row.nama }}</h6>
                            <p class="text-xs text-secondary mb-0">{{ row.jabatan}}</p>
                          </div>
                        </div>
                      </td>
                      <td>
                        <p class="text-xs font-weight-bold mb-0">{{ row.unit }}</p>
                        <p class="text-xs font-weight-bold mb-0">{{ row.id_karyawan }}</p>
                      </td>
                      <!-- STATUS -->
                      <td class="align-middle text-center text-sm">
                        <span class="badge badge-sm bg-gradient-success">{{ row.status }}</span>
                      </td>
                      <!-- HARIAN -->
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ convertRp(row.harian) }}</span>
                      </td>
                      <!-- BULANAN -->
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ convertRp(row.bulanan) }}</span>
                      </td>
                      <!-- GAJI POKOK -->
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold" v-if="row.rel_payroll != null">
                          {{ convertRp(Math.round(((row.harian == 0) ? ((row.bulanan / row.rel_payroll.periode_total_hk) * row.total_kerja_count) : (row.harian * row.total_kerja_count) ))) }}
                        </span>
                      </td>
                      <!-- TJ SKILL -->
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ convertRp(row.tj_jabatan_skill) }}</span>
                      </td>
                      <!-- TRANSPORT -->
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ convertRp(row.transport) }}</span>
                      </td>
                      <!-- U. MAKAN -->
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ convertRp((row.makan)) }}</span>
                      </td>
                      <!-- TOTAL ALPA -->
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.total_alpa_count }}</span>
                      </td>
                      <!-- TOTAL HK -->
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.total_kerja_count }}</span>
                      </td>
                      <!-- TOTAL OT -->
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.total_ot_count }}</span>
                      </td>
                      <!-- LEMBUR -->
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ convertRp(row.total_ot_count * ((row.unit == 'Head Quarter') ? 250000 : 22619)) }}</span>
                      </td>
                      <!-- BONUS -->
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">BONUS</span>
                      </td>
                      <!-- TOTAL GAJI -->
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold" v-if="row.rel_payroll != null">
                          {{ convertRp(Math.round(Number(((row.harian == 0) ? ((row.bulanan / row.rel_payroll.periode_total_hk) * row.total_kerja_count) : (row.harian * row.total_kerja_count) ) + row.tj_jabatan_skill + row.transport + (row.makan) + row.total_ot_count * ((row.unit == 'Head Quarter') ? 250000 : 22619))) ) }}
                        </span>
                      </td>
                      <!-- PIUTANG -->
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold" v-if="row.rel_payroll != null">
                          <input style="border: 1px solid transparent; background: transparent; text-align: center;" size="8" v-model="row.rel_payroll.piutang" @change="updatePayroll(row.rel_payroll.id, row.rel_payroll.piutang, row.rel_payroll.pinjaman, row.rel_payroll.kekurangan, row.rel_payroll.status_tf, row.rel_payroll.send_slip)">
                        </span>
                      </td>
                      <!-- PINJAMAN -->
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold" v-if="row.rel_payroll != null">
                          <input style="border: 1px solid transparent; background: transparent; text-align: center;" size="8" v-model="row.rel_payroll.pinjaman" @change="updatePayroll(row.rel_payroll.id, row.rel_payroll.piutang, row.rel_payroll.pinjaman, row.rel_payroll.kekurangan, row.rel_payroll.status_tf, row.rel_payroll.send_slip)">
                        </span>
                      </td>
                      <!-- PPH21 -->
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">PPH21</span>
                      </td>
                      <!-- UPAH BPJS -->
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ convertRp(row.upah_bpjs) }}</span>
                      </td>
                      <!-- JHT -->
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ convertRp(row.jht) }}</span>
                      </td>
                      <!-- JKM -->
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ convertRp(row.jkm) }}</span>
                      </td>
                      <!-- JKK -->
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ convertRp(row.jkk) }}</span>
                      </td>
                      <!-- JP -->
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ convertRp(row.jp) }}</span>
                      </td>
                      <!-- JKS -->
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ convertRp(row.jks) }}</span>
                      </td>
                      <!-- TOTAL POTONGAN BPJS -->
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ convertRp(Number(row.jht + row.jkm + row.jkk + row.jp + row.jks)) }}</span>
                      </td>
                      <!-- TOTAL POTONGAN -->
                      <td class="align-middle text-center">
                        <span v-if="row.rel_payroll != null">
                          <span class="text-secondary text-xs font-weight-bold">{{ convertRp(Number(row.rel_payroll.piutang + row.rel_payroll.pinjaman + row.jht + row.jkm + row.jkk + row.jp + row.jks)) }}</span>
                        </span>
                      </td>
                      <!-- GAJI DITERIMA -->
                      <td class="align-middle text-center" v-if="row.rel_payroll != null">
                        <span class="text-secondary text-xs font-weight-bold">{{ convertRp(Math.round(Number(((row.harian == 0) ? ((row.bulanan / row.rel_payroll.periode_total_hk) * row.total_kerja_count) : (row.harian * row.total_kerja_count) ) + row.tj_jabatan_skill + row.transport + (row.makan) + row.total_ot_count * ((row.unit == 'Head Quarter') ? 250000 : 22619)) - ( Number(row.rel_payroll.piutang + row.rel_payroll.pinjaman) + Number(row.jht + row.jkm + row.jkk + row.jp + row.jks )) )) }}</span>
                      </td>
                      <!-- STATUS TF -->
                      <td class="align-middle text-center">
                        <span v-if="row.rel_payroll != null">
                          <input type="checkbox" v-model="row.rel_payroll.status_tf" @change="updatePayroll(row.rel_payroll.id, row.rel_payroll.piutang, row.rel_payroll.pinjaman, row.rel_payroll.kekurangan, row.rel_payroll.status_tf, row.rel_payroll.send_slip)">
                        </span>
                      </td>
                      <!-- KEKURANGAN -->
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold" v-if="row.rel_payroll != null">
                          <input style="border: 1px solid transparent; background: transparent; text-align: center;" size="8" v-model="row.rel_payroll.kekurangan" @change="updatePayroll(row.rel_payroll.id, row.rel_payroll.piutang, row.rel_payroll.pinjaman, row.rel_payroll.kekurangan, row.rel_payroll.status_tf, row.rel_payroll.send_slip)">
                        </span>
                      </td>
                      <!-- NO REK -->
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.no_rek }}</span>
                      </td>
                      <!-- AN REK -->
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.an_rek }}</span>
                      </td>
                      <!-- BANK -->
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.bank }}</span>
                      </td>
                      <!-- DETAIL SLIP -->
                      <td class="align-middle text-center text-sm">
                        <router-link :to="/detail-slip/+row.id_karyawan+'/'+search.periode_start+'/'+search.periode_end"><span class="badge badge-sm bg-gradient-info">Slip</span></router-link>
                      </td>
                      <!-- SEND SLIP -->
                      <td class="align-middle text-center">
                        <span v-if="row.rel_payroll != null">
                          <input type="checkbox" v-model="row.rel_payroll.send_slip" @change="updatePayroll(row.rel_payroll.id, row.rel_payroll.piutang, row.rel_payroll.pinjaman, row.rel_payroll.kekurangan, row.rel_payroll.status_tf, row.rel_payroll.send_slip)">
                        </span>
                      </td>
                    </tr>
                    <!-- <tr>
                      <td colspan="" style="background-color: #F8F8FF; text-align: center;">TOTAL</td>
                      <td style="background-color: #F8F8FF; text-align: center;">{{ totalSakitAll }}</td>
                      <td style="background-color: #F8F8FF; text-align: center;">{{ totalIjinAll }}</td>
                      <td style="background-color: #F8F8FF; text-align: center;">{{ totalAlpaAll }}</td>
                      <td style="background-color: #F8F8FF; text-align: center;">{{ totalCutiAll }}</td>
                      <td style="background-color: #F8F8FF; text-align: center;">{{ totalKerjAll }}</td>
                      <td style="background-color: #F8F8FF; text-align: center;">{{ totalOTAll }}</td>
                    </tr> -->
                  </tbody>
                </table>
              </div>
            </div>
            <div class="card-footer">
            </div>
          </div>
      </div>
    </div>
  </div>

  <!-- =======  MODAL FILTER ======= -->
  <div class="container">
    <vue-final-modal v-model="formFilter.show" classes="modal-container" content-class="modal-content" :z-index="10000">
      <!-- header -->
      <div class="row">
        <div class="col-11 float-left">
          <span class="modal__title">{{formFilter.title}}</span>
        </div>
        <div class="col-1 float-right">
          <i style="cursor: pointer;" class="fa fa-times" aria-hidden="true" @click="formFilter.show = false"></i>
        </div>
      </div><hr>
      <!-- end header -->
      <div class="modal__content container">
        <label for="example-text-input" class="form-control-label">Nama <span style="color: red;">*</span></label>
        <input type="text" class="form-control" placeholder="Nama" v-model="search.nama" required>
        <label for="example-text-input" class="form-control-label mt-3">Periode Start <span style="color: red;">*</span></label>
        <input type="date" class="form-control" placeholder="Nama" v-model="search.periode_start" required>
        <label for="example-text-input" class="form-control-label mt-3">Periode End <span style="color: red;">*</span></label>
        <input type="date" class="form-control" placeholder="Nama" v-model="search.periode_end" required>
      </div>
      <!-- footer -->
      <div class="row float-right mt-5">
        <div class="col-6"> 
        </div>
        <div class="col-2" style="margin-right: 20px;">
          <argon-button  variant="gradient" color="secondary" size="sm" width="1" @click="formFilter.show = false">Close</argon-button>
        </div>
        <div class="col-1">
          <argon-button variant="gradient" color="success" size="sm" width="1" @click="get()" :disabled="onLoading == true">
            <span v-if="onLoading"><i class="fa fa-spinner fa-spin"></i> Please Wait...</span>
            <span v-else>
                <span> Filter</span>
            </span>
          </argon-button>
        </div>
      </div>
      <!-- end footer -->
    </vue-final-modal>
   </div>
</template>

<script>
import ArgonButton from "@/components/ArgonButton.vue";
import { VueFinalModal } from 'vue-final-modal'
import Api from '@/helpers/api';
import config from '@/configs/config';
import dataPayroll from '@/services/dataPayroll.service';
var moment = require('moment');

export default {
  name: "tables",
  components: {
    ArgonButton,
    VueFinalModal,
  },
  data() {
    return {
      moment:moment,
      absenKaryawan: {
        data: []
      },
      formFilter: {
        add: true,
        title: "Filter",
        show: false
      },
      onLoading: false,
      onLoadingSendSlip: false,
      // karyawan: {},
      // totalSakitAll: '',
      // totalIjinAll: '',
      // totalAlpaAll: '',
      // totalCutiAll: '',
      // totalKerjAll: '',
      // totalOTAll: '',
      search: {
        nama: '',
        periode_start: '',
        periode_end: '',
      },
      backgroundRed: null,
      apiUrl :config.apiUrl,
    };
  },
  mounted(){
    this.get();
    this.tokenApi = 'Bearer '+localStorage.getItem('token');
  },
  methods: {
    get(){
      let context = this; 
      this.onLoading = true;
                    
      Api(context, dataPayroll.index({nama: context.search.nama, periode_start: context.search.periode_start, periode_end: context.search.periode_end,})).onSuccess(function(response) {    
          context.absenKaryawan.data = response.data.data.payroll;
      }).onError(function(error) {                    
          if (error.response.status == 404) {
              context.table.data = [];
          }
      }).onFinish(function() { 
         context.onLoading = false;
         context.formFilter.show  = false;
      })
      .call()
    },
    filter() {
      this.formFilter.add   = true;
      this.formFilter.show  = true;
      this.formFilter.title = "Filter";
    },
    updatePayroll(id, piutang, pinjaman, kekurangan, status_tf, send_slip){
      let api     = null;
      let context = this;

      api = Api(context, dataPayroll.updatePayroll({
          id: id,
          piutang: piutang,
          pinjaman: pinjaman,
          kekurangan: kekurangan,
          status_tf: status_tf,
          send_slip: send_slip,
      }));
      api.onSuccess(function(response) {
          // context.notifyVue(response.data.message, 'top', 'right', 'info')
      }).onError(function(error) { 
          // context.notifyVue('Update Failed', 'top', 'right', 'danger')
      }).onFinish(function() {  
          context.get();
      })
      .call();
    },
    sendSlip(){
      let context    = this;       
      this.onLoadingSendSlip = true;

      Api(context, dataPayroll.sendSlip({periode_start: context.search.periode_start, periode_end: context.search.periode_end})).onSuccess(function(response) {    
          // context.absenKaryawan.data = response.data;
      }).onError(function(error) {                    
          if (error.response.status == 404) {
              // context.table.data = [];
          }
      }).onFinish(function() {  
          context.onLoadingSendSlip = false;
      })
      .call()
    },
    setBg(id) {
      this.backgroundRed = id
    },
    convertRp(bilangan) {
      if (bilangan) {
        var number_string = bilangan.toString(),
            sisa    = number_string.length % 3,
            rupiah  = number_string.substr(0, sisa),
            ribuan  = number_string.substr(sisa).match(/\d{3}/g);

        if(ribuan){
          var separator = sisa ? '.' : '';
          rupiah += separator + ribuan.join('.');
          return rupiah
        }else{
          return bilangan
        }
      }
    },
  }
};
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
  height: 600px; 
}
.scroll thead th { 
  position: sticky; 
  top: 0; 
  z-index: 100; 
  background-color: #6495ED; 
}
</style>
