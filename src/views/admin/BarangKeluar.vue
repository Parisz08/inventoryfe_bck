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
                ><i class="fa fa-plus fa-sm" aria-hidden="true"></i> Add New</argon-button>
              </div>
            </div>
            
            <div class="card-body px-0 pt-0 pb-2 mt-4">
              <div class="table-responsive p-0 scroll">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">No Barang Keluar</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Material Code</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Material Name</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Qty</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Description</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Divisi</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Di Serahkan</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Di Setujui</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Di Terima</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Date</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Created By</th>
                      <th class="text-secondary"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(row, i) in table.data" :key="i">
                      <td class="align-middle text-center text-sm">
                        <span class="badge badge-sm bg-gradient-info" style="cursor: pointer;" @click="create(), dataSuratPengambilan.no_sj = row.no_sj, getMaterial()">{{ row.no_sj }}</span>
                      </td>
                      <td class="align-middle text-center text-sm">
                        <span class="badge badge-sm bg-gradient-success">{{ row.material_code }}</span>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ row.material_name }}</span>
                      </td>
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
                      <td>
                        <i class="fas fa-edit fa-sm" aria-hidden="true" style="cursor: pointer; margin-right: 20px;" @click="edit(row.id)" title="Edit"></i>
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
          <a style="margin-top: -40px;" class="btn btn-xs btn-warning d-none d-md-block" :href="apiUrl+'print-pdf/spl?code_spl='" target="_BLANK"><i class="fa fa-print fa-sm"></i> Print</a>
        </div><hr>
        <div class="row mt-4">
          <div class="col">
            <!-- ==================== HEADER ==================== -->
            <div class="row">
              <div class="col-6">
                <p style="margin-top: 0px;">
                  No Pengambilan &ensp;: <input style="border: 1px solid white;" v-model="dataSuratPengambilan.no_sj" size="15" @keyup="getMaterial()"><br>
                  Tanggal &ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;: <input type="date" style="border: 1px solid white;" v-model="dataSuratPengambilan.date" size="2">
                </p>
              </div>
              <div class="col-6">
                <p>Divisi &ensp;: &ensp;&ensp;
                  <input type="checkbox" style="border: 1px solid white;"> Pipa &ensp;&ensp;&ensp;&ensp;&ensp; &ensp;<input type="checkbox" style="border: 1px solid white;"> Slitting<br>
                  &ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;<input type="checkbox" style="border: 1px solid white;"> Shearing &ensp;&ensp;<input type="checkbox" style="border: 1px solid white;"> Maintenance
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
                      <span class="text-secondary text-xs font-weight-bold"> {{ row.qty }} </span>
                    </td>
                    <td class="align-middle text-center">
                      <span class="text-secondary text-xs font-weight-bold"> {{ row.unit }} </span>
                    </td>
                    <td class="align-middle text-center">
                      <span class="text-secondary text-xs font-weight-bold"> {{ row.description }} </span>
                    </td>
                    <td style="text-align: center;">
                      <i class="fa fa-times-circle fa-lg" aria-hidden="true" title="Delete" style="cursor: pointer;" @click="removeAnggota(row.id)"></i>
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
                <p style="text-align: center;"><input style="border: 1px solid white; text-align: center; border-bottom: 1px solid black;" v-model="dataSuratPengambilan.diserahkan"></p>
                <p style="margin-top: -15px; text-align: center;">Bg. Gudang</p>
              </div>
              <div class="col-4">
                <p style="margin-bottom: 50px; text-align: center;">Disetujui Oleh</p>
                <p style="text-align: center;"><input style="border: 1px solid white; text-align: center; border-bottom: 1px solid black;" v-model="dataSuratPengambilan.disetujui"></p>
                <p style="margin-top: -15px; text-align: center;">User</p>
              </div>
              <div class="col-4">
                <p style="margin-bottom: 50px; text-align: center;">Diterima Oleh</p>
                <p style="text-align: center;"><input style="border: 1px solid white; text-align: center; border-bottom: 1px solid black;" v-model="dataSuratPengambilan.diterima"></p>
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
import barangKeluar from '@/services/barangKeluar.service';
var moment = require('moment');

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
      dataImport: '',
      onLoading: false,
      tabelError: {
        data: []
      },
      storageUrl : config.storageUrl,
      dataBarangMasuk: {},
      dataSuratPengambilan: {},
      search: {
        no_sj: '',
        material_code: '',
        qty: '',
        description: '',
        divisi: '',
        diserahkan: '',
        disetujui: '',
        diterima: '',
        date: '',
      },
      searchMaterial: '',
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
      Api(context, barangKeluar.index({no_sj: context.search.no_sj, material_code: context.search.material_code, qty: context.search.qty, description: context.search.description, divisi: context.search.divisi, diserahkan: context.search.diserahkan, disetujui: context.search.disetujui, diterima: context.search.diterima, date: context.search.date})).onSuccess(function(response) {    
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
      alert(id)
      let api      = null;
      let context  = this;
      let formData = new FormData();
      this.onLoading = true;

      if (context.dataSuratPengambilan.no_sj != undefined && context.dataSuratPengambilan.date != undefined && context.dataSuratPengambilan.diserahkan != undefined && context.dataSuratPengambilan.disetujui != undefined && context.dataSuratPengambilan.diterima != undefined) {
        formData.append('id', id);
        formData.append('no_sj', context.dataSuratPengambilan.no_sj);
        formData.append('divisi', 'Slitting');
        formData.append('date', context.dataSuratPengambilan.date);
        formData.append('diserahkan', context.dataSuratPengambilan.diserahkan);
        formData.append('disetujui', context.dataSuratPengambilan.disetujui);
        formData.append('diterima', context.dataSuratPengambilan.diterima);
      }else{
        context.onLoading = false;
        return alert('Field Bintang Merah Wajib Di Isi')
      }

      api = Api(context, barangKeluar.create(formData));

      // eslint-disable-next-line no-unused-vars
      api.onSuccess(function(response) {
        context.getDataCuti();
        context.notify((context.formSPL === 'Add Material') ? 'Data Berhasil di Simpan' : 'Data Berhasil di Update', 'success')
      // eslint-disable-next-line no-unused-vars
      }).onError(function(error) { 
        context.notify((context.formSPL != 'Add Material') ? 'Data Gagal di Simpan' : 'Data Gagal di Update', 'error')                   
      }).onFinish(function() {
        context.onLoading = false;
        context.get()
      })
      .call();
    },
    getMaterial(){
      let context = this;               
      Api(context, barangKeluar.getMaterial({no_sj : context.dataSuratPengambilan.no_sj})).onSuccess(function(response) {   
          context.tableMaterial.data = response.data.data;
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
    // save(){
    //   let api      = null;
    //   let context  = this;
    //   let formData = new FormData();

    //   if (context.dataBarangMasuk.material_name != undefined && context.dataBarangMasuk.type != undefined && context.dataBarangMasuk.unit != undefined && context.dataBarangMasuk.qty != undefined && context.dataBarangMasuk.min_stock != undefined && context.dataBarangMasuk.storage_location != undefined && context.dataBarangMasuk.date != undefined) {
    //     formData.append('material_code', context.dataBarangMasuk.material_code);
    //     formData.append('material_name', context.dataBarangMasuk.material_name);
    //     formData.append('type', context.dataBarangMasuk.type);
    //     formData.append('unit', context.dataBarangMasuk.unit);
    //     formData.append('qty', context.dataBarangMasuk.qty);
    //     if (context.form.title == 'Add Data') {
    //       formData.append('stock_barang', (context.dataBarangMasuk.qty + ((context.dataBarangMasuk.stock_barang == undefined) ? 0 : context.dataBarangMasuk.stock_barang) ));
    //     }else{
    //       formData.append('stock_barang', ((context.dataBarangMasuk.stock_barang - context.dataBarangMasuk.qty_old) + context.dataBarangMasuk.qty) );
    //     }
    //     formData.append('min_stock', context.dataBarangMasuk.min_stock);
    //     formData.append('storage_location', context.dataBarangMasuk.storage_location);
    //     formData.append('note', (this.dataBarangMasuk.note == undefined) ? '' : this.dataBarangMasuk.note);
    //     formData.append('date', context.dataBarangMasuk.date);
    //   }else{
    //     return alert('Field Bintang Merah Wajib Di Isi')
    //   }

    //   if (context.form.title == 'Add Data') {
    //       api = Api(context, barangKeluar.create(formData));
    //   } else {
    //       api = Api(context, barangKeluar.update(this.dataBarangMasuk.id, formData));
    //   }
    //   // eslint-disable-next-line no-unused-vars
    //   api.onSuccess(function(response) {
    //     context.get();
    //     context.form.show = false;
    //     context.notify((context.form === 'Add Data') ? 'Data Berhasil di Simpan' : 'Data Berhasil di Update', 'success')
    //   // eslint-disable-next-line no-unused-vars
    //   }).onError(function(error) { 
    //     context.notify((context.form != 'Add Data') ? 'Data Gagal di Simpan' : 'Data Gagal di Update', 'error')                   
    //   }).onFinish(function() {  
    //   })
    //   .call();
    // },
    remove(id) {
      var r = confirm("Anda yakin ingin menghapus data ini ?");
      if (r == true) {
        let context = this;

        Api(context, barangKeluar.delete(id)).onSuccess(function(response) {
            context.get();
            context.notify('Data Successfully Deleted', 'success')
        }).call();
      }
    },
    // modalImport(){
    //   this.formImport.show  = true;
    //   this.formImport.title = "Import Data Karyawan";
    //   this.tabelError.data  = [];
    // },
    // filesChange(e) {
    //     this.dataImport = e.target.files[0];
    // },
    // importData(){
    //   let api = null;
    //   let context = this;
    //   let formData = new FormData();
    //   this.onLoading = true;

    //   if (this.dataImport != undefined) {
    //     formData.append('import_data', this.dataImport);
    //   }else{
    //     return alert('File Import Not Found')
    //   }

    //   api = Api(context, barangKeluar.import(formData));
    //   api.onSuccess(function(response) {
    //       context.onLoading = false;
    //       context.get();
    //       context.formImport.show = false;
    //       context.notify('Import Data Success', 'success')
    //   }).onError(function(error) {      
    //       context.tabelError.data = error.response.data.data;              
    //       context.notify('Import Data Failed', 'error')
    //       context.onLoading = false;
    //   }).onFinish(function() {  
    //   })
    //   .call();
    // },
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
