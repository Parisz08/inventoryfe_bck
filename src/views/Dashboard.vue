<template>
  <div class="py-4 container-fluid">
    <div class="row">
      <div class="col-lg-12">
        <div class="row">
          <div class="col-lg-3 col-md-6 col-12">
            <card
              title="Total Item"
              :value="totalKaryawan"
              iconClass="ni ni-collection"
              iconBackground="bg-gradient-primary"
              directionReverse
            ></card>
          </div>
          <div class="col-lg-3 col-md-6 col-12">
            <card
              title="Total Barang"
              :value="convertRp(totalGajiBulanIni)"
              iconClass="ni ni-folder-17"
              iconBackground="bg-gradient-danger"
              directionReverse
            ></card>
          </div>
          <div class="col-lg-3 col-md-6 col-12">
            <card
              title="Total Barang Masuk"
              :value="convertRp(totalGajiBulanLalu)"
              iconClass="ni ni-cloud-download-95"
              iconBackground="bg-gradient-success"
              directionReverse
            ></card>
          </div>
          <div class="col-lg-3 col-md-6 col-12">
            <card
              title="Total Barang Keluar"
              :value="totalUsers"
              iconClass="ni ni-cloud-upload-96"
              iconBackground="bg-gradient-warning"
              directionReverse
            ></card>
          </div>
        </div>
        <!-- <div class="row">
          <div class="col-lg-7 mb-lg"> -->
            <!-- line chart -->
          <!--   <div class="card z-index-2">
              <gradient-line-chart />
            </div>
          </div>
          <div class="col-lg-5">
            <carousel />
          </div>
        </div> -->
        <div class="row mt-4">
          <div class="col-lg-12 mb-lg-0 mb-4">
            <div class="card">
              <div class="row mb-4">
                <div class="col-4">
                  <div class="card-header pb-0">
                    <h6>Employee Hour Performance</h6>
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
                  <a class="btn btn-sm btn-warning" style="margin-top: 40px;" :href="apiUrl+'print-pdf/ehp?periode_start='+search.periode_start+'&periode_end='+search.periode_end+'&nama='+search.nama+'&jabatan='+search.jabatan+'&unit='+search.unit+''" target="_BLANK"><i class="fa fa-print fa-sm"></i> Print</a>
                </div>
              </div>
              <div class="table-responsive">
                <table class="table align-items-center">
                  <thead>
                    <tr>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Nama</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder  ps-2">Unit</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">JABATAN</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">JAM KERJA</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">JAM LEMBUR</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">TOTAL JAM</th>
                      <th class="text-secondary"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(row, i) in table.data" :key="i">
                      <td class="w-30">
                        <div class="px-2 py-1 d-flex align-items-center">
                          <!-- <div>
                            <img src="" />
                          </div> -->
                          <div class="ms-4">
                            <p class="mb-0 text-xs font-weight-bold">{{ row.id_karyawan }}</p>
                            <h6 class="mb-0 text-sm">{{ row.nama }}</h6>
                          </div>
                        </div>
                      </td>
                      <td>
                        <div class="text-center">
                          <h6 class="mb-0 text-sm">{{ row.unit }}</h6>
                        </div>
                      </td>
                      <td>
                        <div class="text-center">
                          <h6 class="mb-0 text-sm">{{ row.jabatan }}</h6>
                        </div>
                      </td>
                      <td class="text-sm align-middle">
                        <div class="text-center col">
                          <h6 class="mb-0 text-sm">{{ (8 * row.total_kerja_count) }}</h6>
                        </div>
                      </td>
                      <td class="text-sm align-middle">
                        <div class="text-center col">
                          <h6 class="mb-0 text-sm">{{ row.total_ot_count }}</h6>
                        </div>
                      </td>
                      <td class="text-sm align-middle">
                        <div class="text-center col">
                          <h6 class="mb-0 text-sm">{{ (+(8 * row.total_kerja_count) + +(row.total_ot_count)) }}</h6>
                        </div>
                      </td>
                      <td class="text-secondary"></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <!-- <div class="col-lg-5">
            <categories-card />
          </div> -->
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
              <label for="example-text-input" class="form-control-label mt-3">Periode Start</label>
              <input type="date" class="form-control" placeholder="Periode Start" v-model="search.periode_start">
              <label for="example-text-input" class="form-control-label mt-3">Periode End</label>
              <input type="date" class="form-control" placeholder="Periode End" v-model="search.periode_end">
              <label for="example-text-input" class="form-control-label mt-3">Nama</label>
              <input type="text" class="form-control" placeholder="Nama" v-model="search.nama" required>
              <label for="example-text-input" class="form-control-label mt-3">Jabatan</label>
              <input type="text" class="form-control" placeholder="Jabatan" v-model="search.jabatan" required>
              <label for="example-text-input" class="form-control-label mt-3">Unit</label>
              <input type="text" class="form-control" placeholder="Unit" v-model="search.unit" required>
            </div>
            <!-- footer -->
            <div class="row float-right mt-3">
              <div class="col-6"> 
              </div>
              <div class="col-2" style="margin-right: 20px;">
                <argon-button  variant="gradient" color="secondary" size="sm" width="1" @click="formFilter.show = true">Close</argon-button>
              </div>
              <div class="col-1">
                <argon-button variant="gradient" color="success" size="sm" width="1" @click="getEhp()">Filter</argon-button>
              </div>
            </div>
            <!-- end footer -->
          </vue-final-modal>
         </div>

      </div>
    </div>
  </div>
</template>
<script>
import Card from "@/examples/Cards/Card.vue";
import { VueFinalModal } from 'vue-final-modal'
import ArgonButton from "@/components/ArgonButton.vue";
import Api from '@/helpers/api';
import config from '@/configs/config';
import dashboard from '@/services/dashboard.service';

export default {
  name: "dashboard-default",
  data() {
    return {
      table: {
        data: []
      },
      totalKaryawan: '',
      totalGajiBulanIni: '',
      totalGajiBulanLalu: '',
      totalUsers: '',
      formFilter: {
        add: true,
        title: "Filter",
        show: false
      },
      search: {
        periode_start: '',
        periode_end: '',
        nama: '',
        jabatan: '',
        unit: '',
      },
      apiUrl :config.apiUrl,
    };
  },
  components: {
    Card,
    ArgonButton,
    VueFinalModal,
  },
  mounted(){
    this.get();
    this.getEhp();
  },
  methods: {
    get(param){
      let context = this;               
      Api(context, dashboard.index()).onSuccess(function(response) {    
          context.table.data         = response.data.data.EHP;
          context.totalKaryawan      = response.data.data.totalKaryawan;
          context.totalGajiBulanIni  = response.data.data.totalGajiBulanIni;
          context.totalGajiBulanLalu = response.data.data.totalGajiBulanLalu;
          context.totalUsers         = response.data.data.totalUsers;
      }).onError(function(error) {                    
          if (error.response.status == 404) {
              context.table.data = [];
          }
      }).onFinish(function() { 
         // context.formFilter.show  = false;
      })
      .call()
    },
    getEhp(param){
      let context = this;               
      Api(context, dashboard.showEhp({ periode_start: context.search.periode_start, periode_end: context.search.periode_end, nama: context.search.nama, jabatan: context.search.jabatan, unit: context.search.unit})).onSuccess(function(response) {    
          context.table.data = response.data.data.EHP;
      }).onError(function(error) {                    
          if (error.response.status == 404) {
              context.table.data = [];
          }
      }).onFinish(function() { 
         context.formFilter.show  = false;
      })
      .call()
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
    filter() {
      this.formFilter.add   = true;
      this.formFilter.show  = true;
      this.formFilter.title = "Filter";
      this.onLoading = false;
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
  height: 500px; 
}
.scroll thead th { 
  position: sticky; 
  top: 0; 
  z-index: 100; 
  background-color: #F0F8FF;
}
</style>
