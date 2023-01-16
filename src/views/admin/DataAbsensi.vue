<template>
  <div class="py-4 container-fluid">
    <a :style="search.periode_start == '' ? 'margin-right: 10px; pointer-events: none; cursor: default;' : 'margin-right: 10px;'" style="margin-right: 10px;" :href="apiUrl+'export-excel/absen?periode_start='+search.periode_start+'&periode_end='+search.periode_end+'&nama='+search.nama+''" target="_BLANK"><argon-button :disabled="search.periode_start == ''" color="primary" size="sm" class="mb-3" variant="gradient"><i class="fa fa-file-excel-o" style="margin-right: 5px;"></i> Export Excel</argon-button></a>
    <argon-button color="warning" size="sm" class="mb-3" variant="gradient"><i class="fa fa-upload fa-sm"></i> Import</argon-button>
    <div class=" row">
      <div class="col-12">
          <div class="card"> 
            <div class="row">
              <div class="col-4">
                <div class="card-header pb-0">
                  <h6>Data Absensi</h6>
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
                      <th class="text-uppercase text-xxs font-weight-bolder ">Nama Karyawan</th>
                      <th></th>
                      <th v-for="(row, i) in tableAbsen.data" :key="i" class="text-uppercase text-xxs font-weight-bolder ps-2 text-center" :style="(moment(row.date).locale('id').format('ddd') == 'Sab' || moment(row.date).locale('id').format('ddd') == 'Min') ? 'background-color: red;' : ''">
                        <p class="text-xs font-weight-bold mb-0 text-center">{{moment(row.date).locale('id').format('ddd')}}</p>
                        <p class="text-xs font-weight-bold mb-0 text-center">{{moment(row.date).locale('id').format('DD')}}</p>
                      </th>
                      <th class="text-uppercase text-xxs font-weight-bolder ps-2 text-center" style="background-color: #FF8C00;"><p class="text-xs font-weight-bold mb-0 text-center">S</p></th>
                      <th class="text-uppercase text-xxs font-weight-bolder ps-2 text-center" style="background-color: #FF8C00;"><p class="text-xs font-weight-bold mb-0 text-center">I</p></th>
                      <th class="text-uppercase text-xxs font-weight-bolder ps-2 text-center" style="background-color: #FF8C00;"><p class="text-xs font-weight-bold mb-0 text-center">A</p></th>
                      <th class="text-uppercase text-xxs font-weight-bolder ps-2 text-center" style="background-color: #FF8C00;"><p class="text-xs font-weight-bold mb-0 text-center">C</p></th>
                      <th class="text-uppercase text-xxs font-weight-bolder ps-2 text-center" style="background-color: #FF8C00;"><p class="text-xs font-weight-bold mb-0 text-center">HK</p></th>
                      <th class="text-uppercase text-xxs font-weight-bolder ps-2 text-center" style="background-color: #FF8C00;"><p class="text-xs font-weight-bold mb-0 text-center">OT</p></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(row, i) in absenKaryawan.data" :key="i" @click="setBg(i)" :style="(backgroundRed == i) ? 'background-color: #F0F8FF; cursor: pointer;' : 'cursor: pointer;' ">
                      <td>
                        <div class="d-flex px-2 py-1">
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm">{{ row.nama }}</h6>
                            <p class="text-xs text-secondary mb-0">{{ row.unit}}</p>
                          </div>
                        </div>
                      </td>
                      <td>
                        <p class="text-xs font-weight-bold mb-0 text-center" style="background-color: #FFFACD;">HK</p>
                        <p class="text-xs font-weight-bold mb-0 text-center">OT</p>
                      </td>
                      <td v-for="(value, j) in row.rel_absen_karyawan" :key="j">
                        <p class="text-xs font-weight-bold mb-0 text-center" :style="(value.type_hk == '0') ? 'background-color: #FF6347;' : (value.type_hk == '') ? 'background-color: #FF6347;' : (value.type_hk == 'S') ? 'background-color: #8FBC8F;' : (value.type_hk == 'I') ? 'background-color: #DCDCDC;' : (value.type_hk == 'C') ? 'background-color: yellow;' :'background-color: #FFFACD;'">
                          <input style="border: 1px solid transparent; background: transparent; text-align: center;" size="1" v-model="value.type_hk" @change="updateAbsen(value.id, value.type_hk, 'HK', row.unit, moment(value.date).locale('id').format('ddd'))">
                          <!-- {{moment(value.date).locale('id').format('ddd')}} -->
                        </p>
                        <p class="text-xs font-weight-bold mb-0 text-center">
                          <input style="border: 1px solid transparent; background: transparent; text-align: center;" size="2" v-model="value.type_ot" @change="updateAbsen(value.id, value.type_ot, 'OT', row.unit, moment(value.date).locale('id').format('ddd'))">
                        </p>
                      </td>
                      <td style="background-color: #8FBC8F; text-align: center;">{{row.total_sakit_count}}</td>
                      <td style="background-color: #DCDCDC; text-align: center;">{{row.total_ijin_count}}</td>
                      <td style="background-color: #FF6347; text-align: center;">{{row.total_alpa_count}}</td>
                      <td style="background-color: yellow; text-align: center;">{{row.total_cuti_count}}</td>
                      <td style="background-color: #FFFACD; text-align: center;">{{row.total_kerja_count}}</td>
                      <td style="background-color: #FFD700; text-align: center;">{{row.total_ot_count}}</td>
                    </tr>
                    <tr>
                      <td :colspan="(absenKaryawan.data.length != 0) ? absenKaryawan.data[0].rel_absen_karyawan.length  + 2 : 0" style="background-color: #F8F8FF; text-align: center;">TOTAL </td>
                      <td style="background-color: #F8F8FF; text-align: center;">{{ totalSakitAll }}</td>
                      <td style="background-color: #F8F8FF; text-align: center;">{{ totalIjinAll }}</td>
                      <td style="background-color: #F8F8FF; text-align: center;">{{ totalAlpaAll }}</td>
                      <td style="background-color: #F8F8FF; text-align: center;">{{ totalCutiAll }}</td>
                      <td style="background-color: #F8F8FF; text-align: center;">{{ totalKerjAll }}</td>
                      <td style="background-color: #F8F8FF; text-align: center;">{{ totalOTAll }}</td>
                    </tr>
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

  <!-- =======  MODAL CREATE ======= -->
  <div class="container">
    <vue-final-modal v-model="form.show" classes="modal-container" content-class="modal-content" :z-index="10000">
      <!-- header -->
      <div class="row">
        <div class="col-11 float-left">
          <span class="modal__title">{{form.title}}</span>
        </div>
        <div class="col-1 float-right">
          <i style="cursor: pointer;" class="fa fa-times" aria-hidden="true" @click="form.show = false"></i>
        </div>
      </div><hr>
      <!-- end header -->
      <div class="modal__content container">
        <!-- <label for="example-text-input" class="form-control-label">ID Karyawan <span style="color: red;">*</span></label>
        <input type="text" class="form-control" placeholder="ID Karyawan" v-model="karyawan.id_karyawan">
        <label for="example-text-input" class="form-control-label">Nama <span style="color: red;">*</span></label>
        <input type="text" class="form-control" placeholder="Nama" v-model="karyawan.nama" required>
        <label for="example-text-input" class="form-control-label">Type</label>
        <select class="form-select" aria-label="Default select example" v-model="karyawan.type">
          <option selected>Type</option>
          <option value="HK">Masuk</option>
          <option value="A">Alpa</option>
          <option value="S">Sakit</option>
          <option value="I">Ijin</option>
          <option value="C">Cuti</option>
        </select> -->
        <label for="example-text-input" class="form-control-label">Periode Start <span style="color: red;">*</span></label>
        <input type="date" class="form-control" placeholder="Nama" v-model="karyawan.periode_start" required>
        <label for="example-text-input" class="form-control-label mt-3">Periode End <span style="color: red;">*</span></label>
        <input type="date" class="form-control" placeholder="Nama" v-model="karyawan.periode_end" required>
      </div>
      <!-- footer -->
      <div class="row float-right mt-5">
        <div class="col-6"> 
        </div>
        <div class="col-2" style="margin-right: 20px;">
          <argon-button  variant="gradient" color="secondary" size="sm" width="1" @click="form.show = false">Close</argon-button>
        </div>
        <div class="col-1">
          <argon-button variant="gradient" color="success" size="sm" width="1" @click="save()" :disabled="onLoading == true">
            <span v-if="onLoading"><i class="fa fa-spinner fa-spin"></i> Please Wait...</span>
            <span v-else>
                <span> Save</span>
            </span>
          </argon-button>
        </div>
      </div>
      <!-- end footer -->
    </vue-final-modal>
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
          <argon-button variant="gradient" color="success" size="sm" width="1" @click="getAbsen()" :disabled="onLoading == true">
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
// import dataKaryawan from '@/services/dataKaryawan.service';
import dataAbsensi from '@/services/dataAbsensi.service';
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
      onLoading: false,
      absenKaryawan: {
        data: []
      },
      tableAbsen: {
        data: []
      },
      form: {
        add: true,
        title: "Create Periode Absensi",
        show: false
      },
      formFilter: {
        add: true,
        title: "Filter",
        show: false
      },
      karyawan: {},
      totalSakitAll: '',
      totalIjinAll: '',
      totalAlpaAll: '',
      totalCutiAll: '',
      totalKerjAll: '',
      totalOTAll: '',
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
    // this.get();
    this.getAbsen();
    this.tokenApi = 'Bearer '+localStorage.getItem('token');
  },
  methods: {
    // get(){
    //   let context = this;               
    //   Api(context, dataKaryawan.index({search: this.search})).onSuccess(function(response) {    
    //       context.table.data = response.data.data.data.data;
    //   }).onError(function(error) {                    
    //       if (error.response.status == 404) {
    //           context.table.data = [];
    //       }
    //   })
    //   .call()
    // },
    getAbsen(){
      let context = this;    
      this.onLoading = true;

      Api(context, dataAbsensi.index({nama: context.search.nama, periode_start: context.search.periode_start, periode_end: context.search.periode_end,})).onSuccess(function(response) {    
          context.tableAbsen.data    = response.data.data.data;
          context.absenKaryawan.data = response.data.data.absenKaryawan;

          context.totalSakitAll = response.data.data.totalSakitAll;
          context.totalIjinAll  = response.data.data.totalIjinAll;
          context.totalAlpaAll  = response.data.data.totalAlpaAll;
          context.totalCutiAll  = response.data.data.totalCutiAll;
          context.totalKerjAll  = response.data.data.totalKerjAll;
          context.totalOTAll    = response.data.data.totalOTAll;
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
      this.onLoading = false;
    },
    create() {
      this.form.add   = true;
      this.form.show  = true;
      this.form.title = "Create Periode Absensi";
      this.onLoading = false;
    },
    save(){
      let api      = null;
      let context  = this;
      this.onLoading = true;
      let formData = new FormData();

      if (context.karyawan.periode_start != undefined && context.karyawan.periode_end != undefined) {
        // formData.append('id_karyawan', context.karyawan.id_karyawan);
        // formData.append('nama', context.karyawan.nama);
        // formData.append('type', context.karyawan.type);
        formData.append('periode_start', context.karyawan.periode_start);
        formData.append('periode_end', context.karyawan.periode_end);
      }else{
        return alert('Field Bintang Merah Wajib Di Isi')
      }

      api = Api(context, dataAbsensi.create(formData));
      // eslint-disable-next-line no-unused-vars
      api.onSuccess(function(response) {
        context.getAbsen();
        context.form.show = false;
          // context.notifyVue((context.formTitle === 'Create Periode Absensi') ? 'Data Berhasil di Simpan' : 'Data Berhasil di Update', 'top', 'right', 'info')
      // eslint-disable-next-line no-unused-vars
      }).onError(function(error) {                    
          // context.notifyVue((context.formTitle === 'Create Periode Absensi') ? 'Data Gagal di Simpan' : 'Data Gagal di Update' , 'top', 'right', 'danger')
      }).onFinish(function() { 
         context.onLoading = false; 
      })
      .call();
    },
    updateAbsen(id, value, type, unit_user, type_hari){
      let api     = null;
      let context = this;

      api = Api(context, dataAbsensi.updateAbsen({
          id: id,
          value: value,
          type: type,
          unit_user: unit_user,
          type_hari: type_hari, 
      }));
      api.onSuccess(function(response) {
          // context.notifyVue(response.data.message, 'top', 'right', 'info')
      }).onError(function(error) { 
          // context.notifyVue('Update Failed', 'top', 'right', 'danger')
      }).onFinish(function() {  
          context.getAbsen();
      })
      .call();
    },
    setBg(id) {
      this.backgroundRed = id
    }
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
