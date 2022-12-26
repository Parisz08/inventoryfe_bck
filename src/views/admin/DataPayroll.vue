<template>
  <div class="py-4 container-fluid">
    <argon-button color="info" size="sm" class="mb-3" variant="gradient" style="margin-right: 10px;"><i class="fa fa-download fa-sm"></i> Export</argon-button>
    <argon-button color="warning" size="sm" class="mb-3" variant="gradient"><i class="fa fa-upload fa-sm"></i> Import</argon-button>
    <div class=" row">
      <div class="col-12">
          <div class="card"> 
            <div class="row">
              <div class="col-4">
                <div class="card-header pb-0">
                  <h6>Data Payroll</h6>
                </div>
              </div>
              <div class="col-4">
              </div>
              <div class="col-4 float-right">
                <argon-button
                  style="margin-right: 10px; margin-left: 60px;"
                  class="mt-4"
                  variant="gradient"
                  color="secondary"
                  size="sm"
                  @click="filter()"
                ><i class="fa fa-filter fa-sm" aria-hidden="true"></i> Filter</argon-button>
                <argon-button
                  class="mt-4"
                  variant="gradient"
                  color="success"
                  size="sm"
                  @click="create()"
                ><i class="fa fa-plus fa-sm" aria-hidden="true"></i> Add New</argon-button>
              </div>
            </div>
            
            <div class="card-body px-0 pt-0 pb-2 mt-4">
              <div class="table-responsive p-0 scroll">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr style="color: white;">
                      <th colspan="9" class="text-uppercase text-xxs font-weight-bolder text-center">Basic Data</th>
                      <th rowspan="2" style="background-color: #BDB76B;" class="text-uppercase text-xxs font-weight-bolder align-middle">Lembur</th>
                      <th rowspan="2" style="background-color: #BDB76B;" class="text-uppercase text-xxs font-weight-bolder align-middle">OT</th>
                      <th rowspan="2" style="background-color: #BDB76B;" class="text-uppercase text-xxs font-weight-bolder align-middle">Alpa</th>
                      <th rowspan="2" style="background-color: #BDB76B;" class="text-uppercase text-xxs font-weight-bolder align-middle">HK</th>
                      <th rowspan="2" style="background-color: #BDB76B;" class="text-uppercase text-xxs font-weight-bolder align-middle">Total Gaji</th>
                      <th colspan="10" style="background-color: dimgrey;" class="text-uppercase text-xxs font-weight-bolder text-center">Potongan</th>
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
                      <th class="text-uppercase text-xxs font-weight-bolder ">BPJS</th>
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
                      <td class="align-middle text-center text-sm">
                        <span class="badge badge-sm bg-gradient-success">{{ row.status }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ convertRp(row.harian) }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ convertRp(row.bulanan) }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ convertRp(((row.harian == 0) ? ((row.bulanan / 22) * row.total_kerja_count) : (row.harian * row.total_kerja_count) )) }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ convertRp(row.tj_jabatan_skill) }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ convertRp(row.transport) }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ convertRp((row.makan * row.total_kerja_count)) }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ convertRp(row.total_ot_count * ((row.unit == 'Head Quarter') ? 250000 : 22619)) }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.total_ot_count }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.total_alpa_count }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.total_kerja_count }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ convertRp(Number(((row.harian == 0) ? ((row.bulanan / 22) * row.total_kerja_count) : (row.harian * row.total_kerja_count) ) + row.tj_jabatan_skill + row.transport + (row.makan * row.total_kerja_count) + row.total_ot_count * ((row.unit == 'Head Quarter') ? 250000 : 22619)) - Number(row.total_alpa_count)) }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">POTONGAN</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">PINJAMAN</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">PPH21</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ convertRp(row.upah_bpjs) }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ convertRp(row.upah_bpjs * Number(row.jht)) }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ convertRp(row.upah_bpjs * Number(row.jkm)) }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ convertRp(row.upah_bpjs * Number(row.jkk)) }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ convertRp(row.upah_bpjs * Number(row.jp)) }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ convertRp(Number(row.jks) * Number(4309772)) }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ convertRp(Number(row.upah_bpjs * Number(row.jht) +  row.upah_bpjs * Number(row.jkm) + row.upah_bpjs * Number(row.jkk) + row.upah_bpjs * Number(row.jp) + Number(row.jks) * Number(4309772))) }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ convertRp(Number(((row.harian == 0) ? ((row.bulanan / 22) * row.total_kerja_count) : (row.harian * row.total_kerja_count) ) + row.tj_jabatan_skill + row.transport + (row.makan * row.total_kerja_count) + row.total_ot_count * ((row.unit == 'Head Quarter') ? 250000 : 22619)) - Number(row.total_alpa_count) - Number(row.upah_bpjs * Number(row.jht) +  row.upah_bpjs * Number(row.jkm) + row.upah_bpjs * Number(row.jkk) + row.upah_bpjs * Number(row.jp) + Number(row.jks) * Number(4309772)) ) }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <input type="checkbox" >
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">INPUT</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.no_rek }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.an_rek }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.bank }}</span>
                      </td>
                      <td class="align-middle text-center text-sm">
                        <router-link :to="/detail-slip/+row.id_karyawan"><span class="badge badge-sm bg-gradient-info">Slip</span></router-link>
                      </td>
                      <td class="align-middle text-center">
                        <input type="checkbox" >
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
          <argon-button variant="gradient" color="success" size="sm" width="1" @click="get(), formFilter.show = false">Filter</argon-button>
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
      backgroundRed: null
    };
  },
  mounted(){
    this.get();
    this.tokenApi = 'Bearer '+localStorage.getItem('token');
  },
  methods: {
    get(){
      let context = this;               
      Api(context, dataPayroll.index({nama: context.search.nama, periode_start: context.search.periode_start, periode_end: context.search.periode_end,})).onSuccess(function(response) {    
          context.absenKaryawan.data = response.data.data.payroll;
      }).onError(function(error) {                    
          if (error.response.status == 404) {
              context.table.data = [];
          }
      })
      .call()
    },
    filter() {
      this.formFilter.add   = true;
      this.formFilter.show  = true;
      this.formFilter.title = "Filter";
    },
    updateAbsen(id, value, type){
      let api     = null;
      let context = this;

      api = Api(context, dataPayroll.updateAbsen({
          id: id,
          value: value,
          type: type,
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
