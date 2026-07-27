<template>
  <div class="py-4 container-fluid">
    <a class="btn btn-sm btn-primary" style="margin-right: 10px;" :href="apiUrl+'export-excel/barang-keluar?no_sj='+search.no_sj+'&material_code='+search.material_code+'&material_name='+search.material_name+'&type='+search.type+'&unit='+search.unit+'&description='+search.description+'&divisi='+search.divisi+'&date='+search.date+''" target="_BLANK"><i class="fa fa-download fa-sm"></i> Export</a>
    <!-- <argon-button color="info" size="sm" class="mb-3" variant="gradient" style="margin-right: 10px;"><i class="fa fa-download fa-sm"></i> Export</argon-button> -->
    <!-- <argon-button color="warning" size="sm" class="mb-3" variant="gradient" @click="modalImport()"><i class="fa fa-upload fa-sm"></i> Import</argon-button> -->
    <div class=" row">
      <div class="col-12">
          <div class="card"> 
            <div class="row">
              <div class="col-4">
                <div class="card-header pb-0">
                  <h6>Data Barang Keluar</h6>
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
                  v-if="role == 'Admin'"
                ><i class="fa fa-plus fa-sm" aria-hidden="true"></i> Add New</argon-button>
              </div>
            </div>
            
            <div class="card-body px-0 pt-0 pb-2 mt-4">
              <div class="table-responsive p-0 scroll">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <!-- <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">No</th> -->
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">No Barang Keluar</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Material Code</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Material Name</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Type</th>
                      <!-- <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Unit</th> -->
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Qty</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Description</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Divisi</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Di Serahkan</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Di Setujui</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Di Terima</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Date</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Created By</th>
                      <th class="text-secondary" v-if="role == 'Admin'"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(row, i) in table.data" :key="i">
                      <!-- <td class="align-middle text-center text-sm">
                        <span class="text-secondary text-xs font-weight-bold">{{ i + 1}}</span>
                      </td> -->
                      <td class="align-middle text-center text-sm">
                        <span class="badge badge-sm bg-gradient-info" style="cursor: pointer;" @click="create(), dataSuratPengambilan.no_sj = row.no_sj, getMaterial()">{{ row.no_sj }}</span>
                      </td>
                      <td class="align-middle text-center text-sm">
                        <router-link :to="/detail-barang/+row.material_code">
                          <span class="badge badge-sm bg-gradient-primary">{{ row.material_code }}</span>
                        </router-link>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.material_name }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.type }}</span>
                      </td>
                      <!-- <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.unit }}</span>
                      </td> -->
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.qty}}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.description}}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.divisi }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.diserahkan }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.disetujui }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.diterima }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ moment(row.date).locale('id').format('LL') }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.created_by }}</span>
                      </td>
                      <td v-if="role == 'Admin'">
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

  <!-- =======  MODAL CREATE PENGAMBILAN BARANG ======= -->
  <div class="container">
    <vue-final-modal v-model="form.show" classes="modal-container" content-class="modal-content-width" :z-index="10000">
      <!-- header -->
      <div class="row">
        <div class="col-11 float-left">
          <span class="modal__title"></span>
        </div>
        <div class="col-1 float-right">
          <i style="cursor: pointer;" class="fa fa-times" aria-hidden="true" @click="form.show = false"></i>
        </div>
      </div><hr>
      <!-- end header -->
      <div class="modal__content container">
        <h5 class="text-center font-weight-bold mb-3" style="margin-top: 0px;">Surat Pengambilan Barang</h5>
        <div class="float-end">
          <a style="margin-top: -40px;" class="btn btn-xs btn-warning d-none d-md-block" :href="apiUrl+'print-pdf/surat-barang-keluar?no_sj='+dataSuratPengambilan.no_sj+''" target="_BLANK"><i class="fa fa-print fa-sm"></i> Print</a>
        </div><hr>
        <div class="row mt-4">
          <div class="col">
            <!-- ==================== HEADER ==================== -->
            <div class="row">
              <div class="col-6">
                <p style="margin-top: 0px;">
                  No Pengambilan &ensp;: <input style="border: 1px solid white;" v-model="dataSuratPengambilan.no_sj" size="15" @keyup="getMaterial()" :disabled="tableMaterial.data.length !== 0"><br>
                  Tanggal &ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;: <input type="date" style="border: 1px solid white;" v-model="dataSuratPengambilan.date" size="2" :disabled="tableMaterial.data.length !== 0">
                </p>
              </div>
              <div class="col-6">
                <p>Divisi &ensp;: &ensp;&ensp;
                  <select class="form-select" aria-label="Default select example" v-model="dataSuratPengambilan.divisi" :disabled="tableMaterial.data.length !== 0">
                    <option selected>Select Divisi</option>
                    <option value="Pipa">Pipa</option>
                    <option value="Slitting">Slitting</option>
                    <option value="Shearing">Shearing</option>
                    <option value="Maintenance">Maintenance</option>
                  </select>
                </p>

              </div>
            </div><hr>
            <!-- ============== TEBEL MATERIAL ITEM ============== -->
            <!-- <div class="float-left"> -->
            <argon-button color="success" size="xs" class="mb-2"  @click="createAddMaterial()"><i class="fa fa-plus fa-sm" aria-hidden="true"></i> Tambah Material</argon-button>
            <!-- </div> -->
            <div class="table-responsive p-0 scroll">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr style="background-color: #F0F8FF;">
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">NO</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">NAMA BARANG / MATERIAL</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">QTY</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">SATUAN</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">KETERANGAN</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(row, i) in tableMaterial.data" :key="i">
                    <td class="align-middle text-center text-sm">
                      <span class="text-secondary text-xs font-weight-bold"> {{ i + 1 }} </span>
                    </td>
                    <td class="align-middle text-center text-sm">
                      <span class="text-secondary text-xs font-weight-bold"> {{ row.material_name }} </span>
                    </td>
                    <td class="align-middle text-center">
                      <span class="text-secondary text-xs font-weight-bold"> 
                        <input type="number" style="border: 1px solid white; text-align: center;" size="1" min="1" :value="row.qty" @change="updateQtyMaterial(row.id)" > 
                      </span>
                    </td>
                    <td class="align-middle text-center">
                      <span class="text-secondary text-xs font-weight-bold"> {{ row.unit }} </span>
                    </td>
                    <td class="align-middle text-center">
                      <span class="text-secondary text-xs font-weight-bold">
                        <input type="text" style="border: 1px solid white; text-align: center;" size="50" :value="row.description" @change="updateDescMaterial(row.id)" >
                      </span>
                    </td>
                    <td style="text-align: center;">
                      <i class="fa fa-times-circle fa-lg" aria-hidden="true" title="Delete" style="cursor: pointer;" @click="remove(row.id)"></i>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div><hr>
            <p class="font-weight-bold">Catatan :</p>
            <p style="margin-top: -18px;">Barang / Material tersebut telah di terima dalam keadaan baik.</p>

            <!-- ==================== FOOTER ==================== -->
            <div class="row mt-5">
              <div class="col-4">
                <p style="margin-bottom: 50px; text-align: center;">Diserahkan Oleh</p>
                <p style="text-align: center;"><input style="border: 1px solid white; text-align: center; border-bottom: 1px solid black;" v-model="dataSuratPengambilan.diserahkan" :disabled="tableMaterial.data.length !== 0"></p>
                <p style="margin-top: -15px; text-align: center;">Bg. Gudang</p>
              </div>
              <div class="col-4">
                <p style="margin-bottom: 50px; text-align: center;">Disetujui Oleh</p>
                <p style="text-align: center;"><input style="border: 1px solid white; text-align: center; border-bottom: 1px solid black;" v-model="dataSuratPengambilan.disetujui" :disabled="tableMaterial.data.length !== 0"></p>
                <p style="margin-top: -15px; text-align: center;">User</p>
              </div>
              <div class="col-4">
                <p style="margin-bottom: 50px; text-align: center;">Diterima Oleh</p>
                <p style="text-align: center;"><input style="border: 1px solid white; text-align: center; border-bottom: 1px solid black;" v-model="dataSuratPengambilan.diterima" :disabled="tableMaterial.data.length !== 0"></p>
                <p style="margin-top: -15px; text-align: center;">Penerima Barang</p>
              </div>
            </div>
            <div class="text-center mt-5">
              <div class="">
                <argon-button color="success" variant="gradient" size="xs mb-4" width="1" @click="generateSJNo(), dataSuratPengambilan = {}, form.show = false"> <span style="margin-left: 400px; margin-right: 400px;"><i class="fa fa-check" aria-hidden="true"></i> Selesai</span></argon-button>
              </div>
            </div>

          </div>
        </div>
      </div>
      <!-- end footer -->
    </vue-final-modal>
   </div>

   <!-- =======  MODAL ADD MATERIAL ======= -->
  <div class="container">
    <vue-final-modal v-model="formAddMaterial.show" classes="modal-container" content-class="modal-content" :z-index="10000">
      <!-- header -->
      <div class="row">
        <div class="col-11 float-left">
          <span class="modal__title">{{formAddMaterial.title}}</span>
        </div>
        <div class="col-1 float-right">
          <i style="cursor: pointer;" class="fa fa-times" aria-hidden="true" @click="formAddMaterial.show = false"></i>
        </div>
      </div><hr>
      <!-- end header -->
      <div class="modal__content container">
        <label for="example-text-input" class="form-control-label mt-3">Search Material </label>
        <input type="text" class="form-control mb-3" placeholder="Keyword : Material Code / Material Name / Type" v-model="searchMaterial" @keyup="cariMaterial()">
        <div class="table-responsive p-0 scroll">
          <table class="table align-items-center mb-0">
            <thead>
              <tr style="background-color: #F0F8FF;">
                <th></th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Material Code</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Material Name</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Type</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Storage Location</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(row, i) in tableSearchMaterial.data" :key="i">
                <td class="align-middle text-center text-sm">
                  <i class="fa fa-plus-square text-primary" title="Add Material" style="cursor: pointer;" @click="addMaterial(row.id)"></i>
                </td>
                <td class="align-middle text-center text-sm">
                  <span class="text-secondary text-xs font-weight-bold"> {{ row.material_code }} </span>
                </td>
                <td class="align-middle text-center">
                  <span class="text-secondary text-xs font-weight-bold"> {{ row.material_name }} </span>
                </td>
                <td class="align-middle text-center">
                  <span class="text-secondary text-xs font-weight-bold"> {{ row.type }} </span>
                </td>
                <td class="align-middle text-center">
                  <span class="text-secondary text-xs font-weight-bold"> {{ row.storage_location }} </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <!-- footer -->
      <div class="row float-right mt-3">
        <div class="col-6"> 
        </div>
        <div class="col-2" style="margin-right: 20px;">
        </div>
        <div class="col-1">
          <argon-button  variant="gradient" color="secondary" size="sm" width="1" @click="formAddMaterial.show = true">Close</argon-button>
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
        <label for="example-text-input" class="form-control-label mt-3">No Barang Keluar</label>
        <input type="text" class="form-control" placeholder="No Barang Keluar" v-model="search.no_sj">
        <label for="example-text-input" class="form-control-label mt-3">Material Code</label>
        <input type="text" class="form-control" placeholder="Material Code" v-model="search.material_code" required>
        <label for="example-text-input" class="form-control-label mt-3">Material Name</label>
        <input type="text" class="form-control" placeholder="Material Name" v-model="search.material_name">
        <label for="example-text-input" class="form-control-label mt-3">Type</label>
        <select class="form-select" aria-label="Default select example" v-model="search.type">
          <option selected>Select Type</option>
          <option value="Consumable">Consumable</option>
          <option value="Sparepart">Sparepart</option>
          <option value="Tools">Tools</option>
          <option value="Other">Other</option>
        </select>
        <label for="example-text-input" class="form-control-label mt-3">Description</label>
        <input type="text" class="form-control" placeholder="Description" v-model="search.description" required>
        <label for="example-text-input" class="form-control-label mt-3">Divisi</label>
        <select class="form-select" aria-label="Default select example" v-model="search.divisi">
          <option selected>Select Divisi</option>
          <option value="Pipa">Pipa</option>
          <option value="Slitting">Slitting</option>
          <option value="Shearing">Shearing</option>
          <option value="Maintenance">Maintenance</option>
        </select>
        <label for="example-text-input" class="form-control-label mt-3">Date Range</label>
        <flat-pickr :config="{ mode: 'range',}" class="form-control" v-model="search.date"/>
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
import barangKeluar from '@/services/barangKeluar.service';
import akun from '@/services/akun.service';
var moment = require('moment');
import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';

export default {
  name: "tables",
  components: {
    ArgonButton,
    VueFinalModal,
    flatPickr,
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
      formFilter: {
        add: true,
        title: "Filter",
        show: false
      },
      formAddMaterial: {
        add: true,
        title: "Add Material",
        show: false
      },
      tableSearchMaterial: {
        data: []
      },
      tableMaterial: {
        data: []
      },
      onLoading: false,
      storageUrl : config.storageUrl,
      dataBarangMasuk: {},
      dataSuratPengambilan: {},
      search: {
        no_sj: '',
        material_name: '',
        material_code: '',
        type: '',
        unit: '',
        description: '',
        divisi: '',
        diserahkan: '',
        disetujui: '',
        diterima: '',
        date: '',
      },
      searchMaterial: '',
      apiUrl :config.apiUrl,
      checkedNames: '',
      role: '',
    };
  },
  mounted(){
    this.get();
    this.tokenApi = 'Bearer '+localStorage.getItem('token');
    this.getRole();
  },
  methods: {
    get(param){
      let context = this;               
      Api(context, barangKeluar.index({no_sj: context.search.no_sj, material_code: context.search.material_code, material_name: context.search.material_name, type: context.search.type, unit: context.search.unit, description: context.search.description, divisi: context.search.divisi, diserahkan: context.search.diserahkan, disetujui: context.search.disetujui, diterima: context.search.diterima, date: context.search.date})).onSuccess(function(response) {    
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
    getRole(){
      let context = this;     
      context.onLoading = true;
      Api(context, akun.indexProfile()).onSuccess(function(response) {
          context.role = response.data.data[0].role;
      }).onError(function(error) {  
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
      this.dataSuratPengambilan  = {}
      this.dataSuratPengambilan.diserahkan  = 'Randy Wahyudi'
      this.tableMaterial.data    = []

      if (this.dataSuratPengambilan.no_sj == undefined) {
        this.generateSJNo();
      }
      this.defaultDate()
    },
    createAddMaterial() {
      this.formAddMaterial.add   = true;
      this.formAddMaterial.show  = true;
      this.formAddMaterial.title = "Add Material";
    },
    addMaterial(id){
      let api      = null;
      let context  = this;
      let formData = new FormData();
      this.onLoading = true;

      if (context.dataSuratPengambilan.no_sj != undefined && context.dataSuratPengambilan.date != undefined && context.dataSuratPengambilan.divisi != undefined && context.dataSuratPengambilan.diserahkan != undefined && context.dataSuratPengambilan.disetujui != undefined && context.dataSuratPengambilan.diterima != undefined) {
        formData.append('id', id);
        formData.append('no_sj', context.dataSuratPengambilan.no_sj);
        formData.append('divisi', context.dataSuratPengambilan.divisi);
        formData.append('date', context.dataSuratPengambilan.date);
        formData.append('diserahkan', context.dataSuratPengambilan.diserahkan);
        formData.append('disetujui', context.dataSuratPengambilan.disetujui);
        formData.append('diterima', context.dataSuratPengambilan.diterima);
      }else{
        context.onLoading = false;
        return alert('Divisi, Diserahkan, Disetujui & Diterima  Wajib Di Isi')
      }

      api = Api(context, barangKeluar.create(formData));

      api.onSuccess(function(response) {
        context.notify((context.formSPL === 'Add Material') ? 'Data Berhasil di Simpan' : 'Data Berhasil di Update', 'success')
      }).onError(function(error) { 
        context.notify((context.formSPL != 'Add Material') ? 'Data Gagal di Simpan' : 'Data Gagal di Update', 'error')                   
      }).onFinish(function() {
        context.getMaterial();
        context.get()
      })
      .call();
    },
    getMaterial(){
      let context = this;               
      Api(context, barangKeluar.getMaterial({no_sj : context.dataSuratPengambilan.no_sj})).onSuccess(function(response) {   
          context.tableMaterial.data              = response.data.data;
          context.dataSuratPengambilan.date       = response.data.data[0].date;
          context.dataSuratPengambilan.divisi     = response.data.data[0].divisi;
          context.dataSuratPengambilan.diserahkan = response.data.data[0].diserahkan;
          context.dataSuratPengambilan.disetujui  = response.data.data[0].disetujui;
          context.dataSuratPengambilan.diterima   = response.data.data[0].diterima;
      })
      .call()
    },
    cariMaterial(){
      let context = this;               
      Api(context, barangKeluar.searchMaterial({search : context.searchMaterial})).onSuccess(function(response) {   
          context.tableSearchMaterial.data = response.data.data;
      })
      .call()
    },
    edit(id) {
      let context = this;               
      Api(context, barangKeluar.show(id)).onSuccess(function(response) {
          context.dataBarangMasuk         = response.data.data;
          context.dataBarangMasuk.qty_old = response.data.data.qty;
          context.form.show               = true;
          context.form.title              = 'Edit Data';       
      })
      .call()        
    },
    updateQtyMaterial(id){
      let api      = null;
      let context  = this;
      let formData = new FormData();
      let qty      = event.target.value

      if (id != undefined && qty != undefined) {
        formData.append('qty', qty);
      }else{
        return alert('Field Bintang Merah Wajib Di Isi')
      }

      api = Api(context, barangKeluar.update(id, formData));

      api.onSuccess(function(response) {
        context.notify((context.form === 'Add Data') ? 'Data Berhasil di Simpan' : 'Data Berhasil di Update', 'success')
      }).onError(function(error) { 
        context.notify((context.form != 'Add Data') ? 'Data Gagal di Simpan' : 'Data Gagal di Update', 'error')                   
      }).onFinish(function() {  
        context.getMaterial();
        context.get();
      })
      .call();
    },
    updateDescMaterial(id){
      let api         = null;
      let context     = this;
      let formData    = new FormData();
      let description = event.target.value

      if (id != undefined && description != undefined) {
        formData.append('description', description);
      }else{
        return alert('Field Bintang Merah Wajib Di Isi')
      }

      api = Api(context, barangKeluar.updateDesc(id, formData));

      api.onSuccess(function(response) {
        context.notify((context.form === 'Add Data') ? 'Data Berhasil di Simpan' : 'Data Berhasil di Update', 'success')
      }).onError(function(error) { 
        context.notify((context.form != 'Add Data') ? 'Data Gagal di Simpan' : 'Data Gagal di Update', 'error')                   
      }).onFinish(function() {  
        context.getMaterial();
        context.get();
      })
      .call();
    },
    remove(id) {
      var r = confirm("Anda yakin ingin menghapus data ini ?");
      if (r == true) {
        let context = this;

        Api(context, barangKeluar.delete(id)).onSuccess(function(response) {
            context.getMaterial();
            context.get()
            context.notify('Data Successfully Deleted', 'success')
        }).call();
      }
    },
    generateSJNo(){
      // GENERATE RANDOM STRING
      let length = 8;
      let result = '';
      const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
      const charactersLength = characters.length;
      let counter = 0;
      while (counter < length) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
        counter += 1;
      }

      this.dataSuratPengambilan.no_sj = result;
      // this.getAnggotaToTableSPL();
    },
    defaultDate(){
      var date  = new Date();
      var day   = ("0" + date.getDate()).slice(-2);
      var month = ("0" + (date.getMonth() + 1)).slice(-2);
      var today = date.getFullYear() + "-" + (month) + "-" + (day);

      this.dataSuratPengambilan.date = today
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

/*MODAL WIDTH KUSUS LEBAR BESAR*/
::v-deep .modal-content-width {
  /*display: flex;*/
  /*flex-direction: column;*/
  margin: 0 1rem;
  padding: 1rem;
  border: 1px solid #e2e8f0;
  border-radius: 0.25rem;
  background: #fff;
  width: 1090px;
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
