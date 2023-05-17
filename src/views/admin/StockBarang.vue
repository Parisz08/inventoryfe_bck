<template>
  <div class="py-4 container-fluid">
    <a class="btn btn-sm btn-primary" style="margin-right: 10px;" :href="apiUrl+'export-excel/karyawan?id_karyawan='+search.id_karyawan+'&nama='+search.nama+'&nik='+search.nik+'&jabatan='+search.jabatan+'&unit='+search.unit+'&status='+search.status+''" target="_BLANK"><i class="fa fa-download fa-sm"></i> Export</a>
    <!-- <argon-button color="info" size="sm" class="mb-3" variant="gradient" style="margin-right: 10px;"><i class="fa fa-download fa-sm"></i> Export</argon-button> -->
    <!-- <argon-button color="warning" size="sm" class="mb-3" variant="gradient" @click="modalImport()"><i class="fa fa-upload fa-sm"></i> Import</argon-button> -->
    <div class=" row">
      <div class="col-12">
          <div class="card"> 
            <div class="row">
              <div class="col-4">
                <div class="card-header pb-0">
                  <h6>Stock Barang</h6>
                </div>
              </div>
              <div class="col-6">
              </div>
              <div class="col-2 float-right">
                <argon-button
                  class="mt-4"
                  variant="gradient"
                  color="secondary"
                  size="sm"
                  @click="filter()"
                ><i class="fa fa-filter fa-sm" aria-hidden="true"></i> Filter</argon-button>
                <!-- <argon-button
                  class="mt-4"
                  variant="gradient"
                  color="success"
                  size="sm"
                  @click="create()"
                ><i class="fa fa-plus fa-sm" aria-hidden="true"></i> Add New</argon-button> -->
              </div>
            </div>
            
            <div class="card-body px-0 pt-0 pb-2 mt-4">
              <div class="table-responsive p-0 scroll">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Material Code</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Material Name</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Type</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Unit</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Stock</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Stock In</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Stock Out</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Min Stock</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Storage Location</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Created At</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Created By</th>
                      <!-- <th class="text-secondary"></th> -->
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(row, i) in table.data" :key="i">
                      <td class="align-middle text-center text-sm">
                        <span class="badge badge-sm bg-gradient-success">{{ row.material_code }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.material_name}}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.type}}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.unit}}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="badge badge-sm bg-gradient-success" v-if="row.stock_barang >= row.min_stock">{{ row.stock_barang }}</span>
                        <span class="badge badge-sm bg-gradient-danger" v-if="row.stock_barang < row.min_stock">{{ row.stock_barang }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.total_barang_masuk_count }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.total_barang_keluar_count }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.min_stock }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.storage_location }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ moment(row.created_at).locale('id').format('LL') }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.created_by }}</span>
                      </td>
                      <!-- <td>
                        <i class="fas fa-edit fa-sm" aria-hidden="true" style="cursor: pointer; margin-right: 20px;" @click="edit(row.id)" title="Edit"></i>
                        <i class="fa fa-trash-o fa-sm" aria-hidden="true" title="Hapus" style="cursor: pointer; margin-right: 20px;" @click="remove(row.id)"></i>
                      </td> -->
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
        <label for="example-text-input" class="form-control-label mt-3">Material Code <span style="color: red;">*</span></label>
        <input type="text" class="form-control" placeholder="Material Code" v-model="dataBarangMasuk.material_code" required >

        <label for="example-text-input" class="form-control-label mt-3">Material Name <span style="color: red;">*</span></label>
        <input type="text" class="form-control" placeholder="Material Name" v-model="dataBarangMasuk.material_name" required>

        <label for="example-text-input" class="form-control-label mt-3">Type</label>
        <select class="form-select" aria-label="Default select example" v-model="dataBarangMasuk.type">
          <option selected>Select Type</option>
          <option value="Consumable">Consumable</option>
          <option value="Sparepart">Sparepart</option>
          <option value="Tools">Tools</option>
          <option value="Other">Other</option>
        </select>

        <label for="example-text-input" class="form-control-label mt-3">Unit <span style="color: red;">*</span></label>
        <input type="text" class="form-control" placeholder="Unit" v-model="dataBarangMasuk.unit" required>

        <label for="example-text-input" class="form-control-label mt-3">Qty <span style="color: red;">*</span></label>
        <input type="number" class="form-control" placeholder="Qty" v-model="dataBarangMasuk.qty">

        <label for="example-text-input" class="form-control-label mt-3">Min Stock <span style="color: red;">*</span></label>
        <input type="number" class="form-control" placeholder="Min Stock" v-model="dataBarangMasuk.min_stock">

        <label for="example-text-input" class="form-control-label mt-3">Storage Location <span style="color: red;">*</span></label>
        <input type="text" class="form-control" placeholder="Storage Location" v-model="dataBarangMasuk.storage_location">

        <label for="example-text-input" class="form-control-label mt-3">Note </label>
        <input type="text" class="form-control" placeholder="Note" v-model="dataBarangMasuk.note" required>

        <label for="example-text-input" class="form-control-label mt-3">Date <span style="color: red;">*</span></label>
        <input type="date" class="form-control" placeholder="Date" v-model="dataBarangMasuk.date" required>
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
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
var moment = require('moment');
import stockBarang from '@/services/stockBarang.service';

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
      moment:moment,
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
      dataBarangMasuk: {},
      search: {
        material_code: '',
        material_name: '',
        type: '',
        unit: '',
        storage_location: '',
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
      Api(context, stockBarang.index({material_code: context.search.material_code, material_name: context.search.material_name, type: context.search.type, unit: context.search.unit, storage_location: context.search.storage_location})).onSuccess(function(response) {    
          context.table.data = response.data.data.data;
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
      this.dataBarangMasuk   = {};
    },
    edit(id) {
      let context = this;               
      Api(context, stockBarang.show(id)).onSuccess(function(response) {
          context.dataBarangMasuk = response.data.data;
          context.form.show       = true;
          context.form.title      = 'Edit Data';       
      })
      .call()        
    },
    save(){
      let api      = null;
      let context  = this;
      let formData = new FormData();

      if (context.dataBarangMasuk.material_code != undefined && context.dataBarangMasuk.qty != undefined && context.dataBarangMasuk.note != undefined && context.dataBarangMasuk.date != undefined) {
        formData.append('material_code', context.dataBarangMasuk.material_code);
        formData.append('material_name', context.dataBarangMasuk.material_name);
        formData.append('type', context.dataBarangMasuk.type);
        formData.append('unit', context.dataBarangMasuk.unit);
        formData.append('qty', context.dataBarangMasuk.qty);
        formData.append('min_stock', context.dataBarangMasuk.min_stock);
        formData.append('storage_location', context.dataBarangMasuk.storage_location);
        formData.append('note', (this.dataBarangMasuk.note == undefined) ? '' : this.dataBarangMasuk.note);
        formData.append('date', context.dataBarangMasuk.date);
      }else{
        return alert('Field Bintang Merah Wajib Di Isi')
      }

      if (context.form.title == 'Add Data') {
          api = Api(context, stockBarang.create(formData));
      } else {
          api = Api(context, stockBarang.update(this.dataBarangMasuk.id, formData));
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

        Api(context, stockBarang.delete(id)).onSuccess(function(response) {
            context.get();
            context.notify('Data Successfully Deleted', 'success')
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

      api = Api(context, stockBarang.import(formData));
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
