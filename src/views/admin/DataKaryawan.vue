<template>
  <div class="py-4 container-fluid">
    <a class="btn btn-sm btn-primary" style="margin-right: 10px;" :href="apiUrl+'export-excel/karyawan?id_karyawan='+search.id_karyawan+'&nama='+search.nama+'&nik='+search.nik+'&jabatan='+search.jabatan+'&unit='+search.unit+'&status='+search.status+''" target="_BLANK"><i class="fa fa-download fa-sm"></i> Export</a>
    <!-- <argon-button color="info" size="sm" class="mb-3" variant="gradient" style="margin-right: 10px;"><i class="fa fa-download fa-sm"></i> Export</argon-button> -->
    <argon-button color="warning" size="sm" class="mb-3" variant="gradient" @click="modalImport()"><i class="fa fa-upload fa-sm"></i> Import</argon-button>
    <div class=" row">
      <div class="col-12">
          <div class="card"> 
            <div class="row">
              <div class="col-4">
                <div class="card-header pb-0">
                  <h6>Data Karyawan</h6>
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
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder ">Nama</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder  ps-2">Unit</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Status</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">NIK</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">No HP</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Email</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">No Rek</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">A/N REk</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Bank</th>
                      <!-- <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">NO BPJS TK</th> -->
                      <!-- <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">NO BPJS KES</th> -->
                      <th class="text-secondary"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(row, i) in table.data" :key="i">
                      <td>
                        <router-link :to="/detail-profile/+row.id_karyawan">
                          <div class="d-flex px-2 py-1">
                            <div>
                              <img
                                src="../../assets/img/avatar_karyawan.png"
                                class="avatar avatar-sm me-3"
                                alt="user1"
                              />
                            </div>
                            <div class="d-flex flex-column justify-content-center">
                              <h6 class="mb-0 text-sm">{{ row.nama }}</h6>
                              <p class="text-xs text-secondary mb-0">{{ row.jabatan}}</p>
                            </div>
                          </div>
                        </router-link>
                      </td>
                      <td>
                        <p class="text-xs font-weight-bold mb-0">{{ row.unit }}</p>
                        <p class="text-xs text-secondary mb-0">{{ row.id_karyawan }}</p>
                      </td>
                      <td class="align-middle text-center text-sm">
                        <span class="badge badge-sm bg-gradient-success">{{ row.status }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.nik}}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.no_hp}}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.email}}</span>
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
                      <!-- <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">096747565</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">KAS-K65758</span>
                      </td> -->
                      <td>
                        <!-- <i class="fas fa-edit fa-sm" aria-hidden="true" style="cursor: pointer; margin-right: 20px;" @click="edit(row.id)" title="Edit"></i> -->
                        <i class="fa fa-trash-o fa-sm" aria-hidden="true" title="Hapus" style="cursor: pointer; margin-right: 20px;" @click="remove(row.id)"></i>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="card-footer">
              <!-- <div>
                <argon-pagination class="float-right">
                  <argon-pagination-item prev />
                  <argon-pagination-item label="1" active />
                  <argon-pagination-item label="2" disabled />
                  <argon-pagination-item label="3" />
                  <argon-pagination-item next />
                 </argon-pagination>
              </div> -->
            </div>
          </div>
      </div>
    </div>
  </div>

  <!-- =======  MODAL ADD DATA ======= -->
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
        <!-- <label for="example-text-input" class="form-control-label mt-3">ID Karyawan <span style="color: red;">*</span></label>
        <input type="text" class="form-control" placeholder="ID Karyawan" v-model="karyawan.id_karyawan"> -->
        <label for="example-text-input" class="form-control-label mt-3">Nama <span style="color: red;">*</span></label>
        <input type="text" class="form-control" placeholder="Nama" v-model="karyawan.nama" required>
        <label for="example-text-input" class="form-control-label mt-3">NIK</label>
        <input type="text" class="form-control" placeholder="NIK" v-model="karyawan.nik">
        <label for="example-text-input" class="form-control-label mt-3">Jabatan <span style="color: red;">*</span></label>
        <input type="text" class="form-control" placeholder="Jabatan" v-model="karyawan.jabatan" required>
        <label for="example-text-input" class="form-control-label mt-3">Unit <span style="color: red;">*</span></label>
        <input type="text" class="form-control" placeholder="Unit" v-model="karyawan.unit" required>
        <label for="example-text-input" class="form-control-label mt-3">Status <span style="color: red;">*</span></label>
        <input type="text" class="form-control" placeholder="Status" v-model="karyawan.status" required>
        <!-- <label for="example-text-input" class="form-control-label mt-3">Gaji Pokok <span style="color: red;">*</span></label>
        <input type="number" class="form-control" placeholder="Gaji Pokok" v-model="karyawan.gaji_pokok" required> -->
        <label for="example-text-input" class="form-control-label mt-3">Harian</label>
        <input type="number" class="form-control" placeholder="Harian" v-model="karyawan.harian">
        <label for="example-text-input" class="form-control-label mt-3">Bulanan</label>
        <input type="number" class="form-control" placeholder="Bulanan" v-model="karyawan.bulanan">
        <label for="example-text-input" class="form-control-label mt-3">TJ Jabatan / Skill</label>
        <input type="number" class="form-control" placeholder="TJ Jabatan / Skill" v-model="karyawan.tj_jabatan_skill">
        <label for="example-text-input" class="form-control-label mt-3">Transport</label>
        <input type="number" class="form-control" placeholder="Transport" v-model="karyawan.transport">
        <label for="example-text-input" class="form-control-label mt-3">Makan</label>
        <input type="number" class="form-control" placeholder="Makan" v-model="karyawan.makan">
        <label for="example-text-input" class="form-control-label mt-3">Nama Bank</label>
        <input type="text" class="form-control" placeholder="Nama Bank" v-model="karyawan.bank">
        <label for="example-text-input" class="form-control-label mt-3">No Rekening</label>
        <input type="text" class="form-control" placeholder="No Rekening" v-model="karyawan.no_rek">
        <label for="example-text-input" class="form-control-label mt-3">Atas Nama Rekening</label>
        <input type="text" class="form-control" placeholder="Atas Nama Rekening" v-model="karyawan.an_rek">
        <label for="example-text-input" class="form-control-label mt-3">NO BPJS TK</label>
        <input type="text" class="form-control" placeholder="NO BPJS TK" v-model="karyawan.no_bpjs_tk">
        <label for="example-text-input" class="form-control-label mt-3">NO BPJS KES</label>
        <input type="text" class="form-control" placeholder="NO BPJS KES" v-model="karyawan.no_bpjs_kes">
        <!-- <label for="example-text-input" class="form-control-label mt-3">Total Cuti <span style="color: red;">*</span></label>
        <input type="number" class="form-control" placeholder="Total Cuti" v-model="karyawan.total_cuti" required> -->
      </div>
      <!-- footer -->
      <div class="row float-right mt-3">
        <div class="col-6"> 
        </div>
        <div class="col-2" style="margin-right: 20px;">
          <argon-button  variant="gradient" color="secondary" size="sm" width="1" @click="form.show = true">Close</argon-button>
        </div>
        <div class="col-1">
          <argon-button variant="gradient" color="success" size="sm" width="1" @click="save()">Save</argon-button>
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
        <label for="example-text-input" class="form-control-label mt-3">ID Karyawan</label>
        <input type="text" class="form-control" placeholder="ID Karyawan" v-model="search.id_karyawan">
        <label for="example-text-input" class="form-control-label mt-3">Nama</label>
        <input type="text" class="form-control" placeholder="Nama" v-model="search.nama" required>
        <label for="example-text-input" class="form-control-label mt-3">NIK</label>
        <input type="text" class="form-control" placeholder="NIK" v-model="search.nik">
        <label for="example-text-input" class="form-control-label mt-3">Jabatan</label>
        <input type="text" class="form-control" placeholder="Jabatan" v-model="search.jabatan" required>
        <label for="example-text-input" class="form-control-label mt-3">Unit</label>
        <input type="text" class="form-control" placeholder="Unit" v-model="search.unit" required>
        <label for="example-text-input" class="form-control-label mt-3">Status</label>
        <input type="text" class="form-control" placeholder="Status" v-model="search.status" required>
      </div>
      <!-- footer -->
      <div class="row float-right mt-3">
        <div class="col-6"> 
        </div>
        <div class="col-2" style="margin-right: 20px;">
          <argon-button  variant="gradient" color="secondary" size="sm" width="1" @click="formFilter.show = true">Close</argon-button>
        </div>
        <div class="col-1">
          <argon-button variant="gradient" color="success" size="sm" width="1" @click="get()">Filter</argon-button>
        </div>
      </div>
      <!-- end footer -->
    </vue-final-modal>
   </div>

  <!-- =======  MODAL IMPORT ======= -->
  <div class="container">
    <vue-final-modal v-model="formImport.show" classes="modal-container" content-class="modal-content" :z-index="10000">
    <!-- header -->
    <div class="row">
      <div class="col-11 float-left">
        <span class="modal__title">{{formImport.title}}</span>
      </div>
      <div class="col-1 float-right">
        <i style="cursor: pointer;" class="fa fa-times" aria-hidden="true" @click="formImport.show = false"></i>
      </div>
    </div><hr>
    <!-- end header -->
    <div class="modal__content container">
      <div>
        <label for="example-text-input" class="form-control-label mt-3">Upload File <span style="color: red;">*</span></label>
        <input type="file" class="form-control" placeholder="Upload File" @change="filesChange" style="margin-bottom: 20px;">
        <small>Download Template<a :href="storageUrl+'/template_import/Template Import Data Karyawan.xlsx'" style="color: blue;"> Import Data Karyawan</a></small>
        <div class="mt-2" v-if="tabelError.data.length !== 0 ">
          <table>
            <thead>
              <slot name="columns">
                <tr style="background-color: #F0F8FF;">
                  <th style="font-size: 13px; text-align: center;">Column</th>
                  <th style="font-size: 13px; text-align: center;">Error</th>
                  <th style="font-size: 13px; text-align: center;">Row</th>
                  <th style="font-size: 13px; text-align: center;">Values</th>
                </tr>
              </slot>
            </thead>
            <tbody>
              <tr v-for="(row, i) in tabelError.data" :key="i">
                <td style="font-size: 13px; text-align: center;">{{ row.attribute }}</td>
                <td style="font-size: 13px; text-align: center;">{{ row.errors }}</td>
                <td style="font-size: 13px; text-align: center;">{{ row.row }}</td>
                <td style="font-size: 13px; text-align: center;">{{ row.values }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <!-- footer -->
    <div class="row float-right mt-5">
      <div class="col-6"> 
      </div>
      <div class="col-2" style="margin-right: 20px;">
        <argon-button  variant="gradient" color="secondary" size="sm" width="1" @click="formImport.show = false">Close</argon-button>
      </div>
      <div class="col-1">
        <button type="primary" class="btn btn-sm btn-info btn-fill" @click="importData()" :disabled="onLoading">
          <span v-if="onLoading"><i class="fa fa-spinner fa-spin"></i> Please Wait...</span>
          <span v-else>
              <span>Import</span>
          </span>
        </button>
        <!-- <argon-button variant="gradient" color="success" size="sm" width="1" @click="get(), formImport.show = false">Import</argon-button> -->
      </div>
    </div>
    <!-- end footer -->
    </vue-final-modal>
  </div>
</template>

<script>
import ArgonButton from "@/components/ArgonButton.vue";
import { VueFinalModal } from 'vue-final-modal'
// import ArgonPagination from "@/components/ArgonPagination.vue";
// import ArgonPaginationItem from "@/components/ArgonPaginationItem.vue";
import Api from '@/helpers/api';
import config from '@/configs/config';
import dataKaryawan from '@/services/dataKaryawan.service';

export default {
  name: "tables",
  components: {
    ArgonButton,
    VueFinalModal,
    // ArgonPagination,
    // ArgonPaginationItem,
  },
  data() {
    return {
      table: {
        data: []
      },
      form: {
        add: true,
        title: "Add Data",
        show: false
      },
      formImport: {
        add: true,
        title: "Import Data Karyawan",
        show: false
      },
      formFilter: {
        add: true,
        title: "Filter",
        show: false
      },
      dataImport: '',
      onLoading: false,
      tabelError: {
        data: []
      },
      storageUrl : config.storageUrl,
      karyawan: {},
      search: {
        id_karyawan: '',
        nama: '',
        nik: '',
        jabatan: '',
        unit: '',
        status: '',
      },
      apiUrl :config.apiUrl,
    };
  },
  mounted(){
    this.get();
    this.tokenApi = 'Bearer '+localStorage.getItem('token');
  },
  methods: {
    get(param){
      let context = this;               
      Api(context, dataKaryawan.index({id_karyawan: context.search.id_karyawan, nama: context.search.nama, nik: context.search.nik, jabatan: context.search.jabatan, unit: context.search.unit, status: context.search.status,})).onSuccess(function(response) {    
          context.table.data = response.data.data.data;
      }).onError(function(error) {                    
          if (error.response.status == 404) {
              context.table.data = [];
          }
      }).onFinish(function() { 
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
      this.form.title = "Add Data";
      this.karyawan   = {};
    },
    // edit(id) {
    //   let context = this;               
    //   Api(context, dataKaryawan.show(id)).onSuccess(function(response) {
    //     console.log(response.data.data)
    //       context.karyawan   = response.data.data;
    //       context.form.show  = true;
    //       context.form.title = 'Edit Data';       
    //   })
    //   .call()        
    // },
    save(){
      let api      = null;
      let context  = this;
      let formData = new FormData();

      if (context.karyawan.nama != undefined && context.karyawan.jabatan != undefined && context.karyawan.unit != undefined && context.karyawan.status != undefined) {
        formData.append('id_karyawan', context.karyawan.id_karyawan);
        formData.append('nama', context.karyawan.nama);
        formData.append('nik', (this.karyawan.nik == undefined) ? '' : this.karyawan.nik);
        formData.append('jabatan', context.karyawan.jabatan);
        formData.append('unit', context.karyawan.unit);
        formData.append('status', context.karyawan.status);
        // formData.append('gaji_pokok', context.karyawan.gaji_pokok);
        formData.append('harian', (this.karyawan.harian == undefined) ? '' : this.karyawan.harian);
        formData.append('bulanan', (this.karyawan.bulanan == undefined) ? '' : this.karyawan.bulanan);
        formData.append('tj_jabatan_skill', (this.karyawan.tj_jabatan_skill == undefined) ? '' : this.karyawan.tj_jabatan_skill);
        formData.append('transport', (this.karyawan.transport == undefined) ? '' : this.karyawan.transport);
        formData.append('makan', (this.karyawan.makan == undefined) ? '' : this.karyawan.makan);
        formData.append('bank', (this.karyawan.bank == undefined) ? '' : this.karyawan.bank);
        formData.append('no_rek', (this.karyawan.no_rek == undefined) ? '' : this.karyawan.no_rek);
        formData.append('an_rek', (this.karyawan.an_rek == undefined) ? '' : this.karyawan.an_rek);
        formData.append('no_bpjs_tk', (this.karyawan.no_bpjs_tk == undefined) ? '' : this.karyawan.no_bpjs_tk);
        formData.append('no_bpjs_kes', (this.karyawan.no_bpjs_kes == undefined) ? '' : this.karyawan.no_bpjs_kes);
        // formData.append('total_cuti', context.karyawan.total_cuti);
      }else{
        return alert('Field Bintang Merah Wajib Di Isi')
      }

      if (context.form.title == 'Add Data') {
          api = Api(context, dataKaryawan.create(formData));
      } else {
          api = Api(context, dataKaryawan.update(this.karyawan.id, formData));
      }
      // eslint-disable-next-line no-unused-vars
      api.onSuccess(function(response) {
        context.get();
        context.form.show = false;
          // context.notifyVue((context.formTitle === 'Add Data') ? 'Data Berhasil di Simpan' : 'Data Berhasil di Update', 'top', 'right', 'info')
      // eslint-disable-next-line no-unused-vars
      }).onError(function(error) {                    
          // context.notifyVue((context.formTitle === 'Add Data') ? 'Data Gagal di Simpan' : 'Data Gagal di Update' , 'top', 'right', 'danger')
      }).onFinish(function() {  
      })
      .call();
    },
    remove(id) {
      var r = confirm("Anda yakin ingin menghapus data ini ?");
      if (r == true) {
        let context = this;

        Api(context, dataKaryawan.delete(id)).onSuccess(function(response) {
            context.get();
            // context.notifyVue('Data Berhasil di Hapus', 'top', 'right', 'info')
        }).call();
      }
    },
    modalImport(){
      this.formImport.show  = true;
      this.formImport.title = "Import Data Karyawan";
      this.tabelError.data  = [];
    },
    filesChange(e) {
        this.dataImport = e.target.files[0];
    },
    importData(){
      let api = null;
      let context = this;
      let formData = new FormData();
      this.onLoading = true;

      if (this.dataImport != undefined) {
        formData.append('import_data', this.dataImport);
      }else{
        return alert('File Import Not Found')
      }

      api = Api(context, dataKaryawan.import(formData));
      api.onSuccess(function(response) {
          context.onLoading = false;
          context.get();
          context.formImport.show = false;
          // context.notifyVue('Data Berhasil di Import', 'top', 'right', 'info')
      }).onError(function(error) {      
          context.tabelError.data = error.response.data.data;              
          // context.notifyVue('Data Gagal di Import' , 'top', 'right', 'danger')
          context.onLoading = false;
      }).onFinish(function() {  
      })
      .call();
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
