<template>
  <div class="py-4 container-fluid">
    <!-- <a class="btn btn-sm btn-primary" style="margin-right: 10px;" :href="apiUrl+'export-excel/karyawan?id_karyawan='+search.id_karyawan+'&nama='+search.nama+'&nik='+search.nik+'&jabatan='+search.jabatan+'&unit='+search.unit+'&status='+search.status+''" target="_BLANK"><i class="fa fa-download fa-sm"></i> Export</a> -->
    <!-- <argon-button color="info" size="sm" class="mb-3" variant="gradient" style="margin-right: 10px;"><i class="fa fa-download fa-sm"></i> Export</argon-button> -->
    <!-- <argon-button color="warning" size="sm" class="mb-3" variant="gradient" @click="modalImport()"><i class="fa fa-upload fa-sm"></i> Import</argon-button> -->
    <div class=" row">
      <div class="col-12">
          <div class="card"> 
            <div class="row">
              <div class="col-4">
                <div class="card-header pb-0">
                  <h6>Account</h6>
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
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Karyawan ID</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Full Name</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Username</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Status</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Role</th>
                      <th class="text-secondary"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(row, i) in table.data" :key="i">
                      <td class="align-middle text-center text-sm">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.employee_id }}</span>
                      </td>
                      <td class="align-middle text-center text-sm">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.full_name }}</span>
                      </td>
                      <td class="align-middle text-center text-sm">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.username }}</span>
                      </td>
                      <td class="align-middle text-center text-sm">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.status }}</span>
                      </td>
                      <td class="align-middle text-center text-sm">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.role }}</span>
                      </td>
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
        <label for="example-text-input" class="form-control-label mt-3">Karyawan ID </label>
        <input type="text" class="form-control" placeholder="Karyawan ID" v-model="akun.employee_id" required>
        <label for="example-text-input" class="form-control-label mt-3">Full Name <span style="color: red;">*</span></label>
        <input type="text" class="form-control" placeholder="Full Name" v-model="akun.full_name">
        <label for="example-text-input" class="form-control-label mt-3">Username <span style="color: red;">*</span></label>
        <input type="text" class="form-control" placeholder="Username" v-model="akun.username">
        <label for="example-text-input" class="form-control-label mt-3">Password <span style="color: red;">*</span></label>
        <input type="text" class="form-control" placeholder="Password" v-model="akun.password">
        <label for="example-text-input" class="form-control-label mt-3">Role <span style="color: red;">*</span></label>
        <select class="form-select" aria-label="Default select example" v-model="akun.role">
          <option selected>Type</option>
          <option value="Admin">Admin</option>
          <option value="User">User</option>
          <option value="Purchasing">Purchasing</option>
        </select>
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
        <label for="example-text-input" class="form-control-label mt-3">Nama</label>
        <input type="text" class="form-control" placeholder="Nama" v-model="search" required>
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
        <small>Download Template<a :href="storageUrl+'/template_import/Template Import Data Users.xlsx'" style="color: blue;"> Import Data Setting Approval</a></small>
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
import akun from '@/services/akun.service';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

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
        title: "Import Data Setting Approval",
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
      akun: {},
      search: '',
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
      Api(context, akun.index({search: this.search})).onSuccess(function(response) {    
          context.table.data = response.data.data.data.data;
          console.log(response.data.data.data.data)
          context.notify('Data Retrieved Successfully', 'success')
      }).onError(function(error) {                    
          if (error.response.status == 404) {
              context.table.data = [];
          }
          context.notify('Data Retrieved Failed', 'error')
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
      this.akun   = {};
    },
    // edit(id) {
    //   let context = this;               
    //   Api(context, akun.show(id)).onSuccess(function(response) {
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

      if (context.akun.full_name != undefined && context.akun.username != undefined && context.akun.password != undefined && context.akun.role != undefined) {
        formData.append('employee_id', (context.akun.employee_id == undefined) ? '' : context.akun.employee_id);
        formData.append('full_name', context.akun.full_name);
        formData.append('username', context.akun.username);
        formData.append('password', context.akun.password);
        formData.append('role', context.akun.role);
      }else{
        return alert('Field Bintang Merah Wajib Di Isi')
      }

      if (context.form.title == 'Add Data') {
          api = Api(context, akun.create(formData));
      } else {
          api = Api(context, akun.update(this.karyawan.id, formData));
      }
      // eslint-disable-next-line no-unused-vars
      api.onSuccess(function(response) {
        context.get();
        context.form.show = false;
        context.notify((context.form === 'Add Data') ? 'Data Berhasil di Simpan' : 'Data Berhasil di Update', 'success')
      // eslint-disable-next-line no-unused-vars
      }).onError(function(error) { 
        context.notify((context.form != 'Add Data') ? 'Data Gagal di Simpan' : 'Data Gagal di Update', 'error')                   
      }).onFinish(function() {  
      })
      .call();
    },
    remove(id) {
      var r = confirm("Anda yakin ingin menghapus data ini ?");
      if (r == true) {
        let context = this;

        Api(context, akun.delete(id)).onSuccess(function(response) {
            context.get();
            context.notify('Data Successfully Deleted', 'success')
        }).call();
      }
    },
    modalImport(){
      this.formImport.show  = true;
      this.formImport.title = "Import Data Setting Approval";
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

      api = Api(context, akun.import(formData));
      api.onSuccess(function(response) {
          context.onLoading = false;
          context.get();
          context.formImport.show = false;
          context.notify('Import Data Success', 'success')
      }).onError(function(error) {      
          context.tabelError.data = error.response.data.data;              
          context.notify('Import Data Failed', 'error')
          context.onLoading = false;
      }).onFinish(function() {  
      })
      .call();
    },
    notify(message, type) {
      if (type == 'success') {
        toast.success(message, {
          autoClose: 2000,
        }); // ToastOptions
      }else{
        toast.error(message, {
          autoClose: 2000,
        }); // ToastOptions
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
  height: 500px; 
}
.scroll thead th { 
  position: sticky; 
  top: 0; 
  z-index: 100; 
  background-color: #F0F8FF;
}
</style>
