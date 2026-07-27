<template>
  <div class="py-4 container-fluid">
    <div class="row">
      <div class="col-lg-12">
        <!-- <div class="row">
          <div class="col-lg-3 col-md-6 col-12">
            <card
              title="Total Item"
              :value="convertRp(totalItem)"
              iconClass="ni ni-collection"
              iconBackground="bg-gradient-primary"
              directionReverse
            ></card>
          </div>
          <div class="col-lg-3 col-md-6 col-12">
            <card
              title="Total Barang"
              :value="convertRp(totalBarang)"
              iconClass="ni ni-folder-17"
              iconBackground="bg-gradient-danger"
              directionReverse
            ></card>
          </div>
          <div class="col-lg-3 col-md-6 col-12">
            <card
              title="Total Barang Masuk"
              :value="convertRp(totalBarangMasuk)"
              iconClass="ni ni-cloud-download-95"
              iconBackground="bg-gradient-success"
              directionReverse
            ></card>
          </div>
          <div class="col-lg-3 col-md-6 col-12">
            <card
              title="Total Barang Keluar"
              :value="convertRp(totalBarangKeluar)"
              iconClass="ni ni-cloud-upload-96"
              iconBackground="bg-gradient-warning"
              directionReverse
            ></card>
          </div>
        </div> -->
        
        <!-- TABLE MATERIAL MAX STOCK -->
        <div class="row mt-4">
          <div class="col-lg-12 mb-lg-0 mb-4">
            <div class="card">
              <div class="row mb-4">
                <div class="col-4">
                  <div class="card-header pb-0">
                    <h6>Material Min Stock</h6>
                  </div>
                </div>
                <div class="col-4">
                </div>
                <div class="col-4 float-right">
                  <!-- <argon-button
                    style="margin-right: 10px; margin-left: 60px;"
                    class="mt-4"
                    variant="gradient"
                    color="secondary"
                    size="sm"
                    @click="filter()"
                  ><i class="fa fa-filter fa-sm" aria-hidden="true"></i> Filter</argon-button>
                  <a class="btn btn-sm btn-warning" style="margin-top: 40px;" :href="apiUrl+'print-pdf/ehp?periode_start='+search.periode_start+'&periode_end='+search.periode_end+'&nama='+search.nama+'&jabatan='+search.jabatan+'&unit='+search.unit+''" target="_BLANK"><i class="fa fa-print fa-sm"></i> Print</a> -->
                </div>
              </div>
              <div class="table-responsive scroll">
                <table class="table align-items-center">
                  <thead>
                    <tr>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Material Code</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Material Name</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Type</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Unit</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Stock</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Storage Location</th>
                      <th class="text-secondary"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(row, i) in tableMinStock.data" :key="i">
                      <td>
                        <div class="text-center">
                          <router-link :to="/detail-barang/+row.material_code">
                            <span class="badge badge-sm bg-gradient-primary">{{ row.material_code }}</span>
                          </router-link>
                        </div>
                      </td>
                      <td>
                        <div class="text-center">
                          <h6 class="mb-0 text-sm">{{ row.material_name }}</h6>
                        </div>
                      </td>
                      <td class="text-sm align-middle">
                        <div class="text-center col">
                          <h6 class="mb-0 text-sm">{{ row.type }}</h6>
                        </div>
                      </td>
                      <td class="text-sm align-middle">
                        <div class="text-center col">
                          <h6 class="mb-0 text-sm">{{ row.unit }}</h6>
                        </div>
                      </td>
                      <td class="text-sm align-middle">
                        <div class="text-center col">
                          <span class="badge badge-sm bg-gradient-danger">{{ row.stock_barang }}</span>
                        </div>
                      </td>
                      <td class="text-sm align-middle">
                        <div class="text-center col">
                          <h6 class="mb-0 text-sm">{{ row.storage_location }}</h6>
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

        <!-- TABLE BARANG SERING KELUAR -->
        <div class="row mt-4">
          <div class="col-lg-12 mb-lg-0 mb-4">
            <div class="card">
              <div class="row mb-4">
                <div class="col-4">
                  <div class="card-header pb-0">
                    <h6>Material Sering Terpakai</h6>
                  </div>
                </div>
                <div class="col-4">
                </div>
                <div class="col-4 float-right">
                  <!-- <argon-button
                    style="margin-right: 10px; margin-left: 60px;"
                    class="mt-4"
                    variant="gradient"
                    color="secondary"
                    size="sm"
                    @click="filter()"
                  ><i class="fa fa-filter fa-sm" aria-hidden="true"></i> Filter</argon-button>
                  <a class="btn btn-sm btn-warning" style="margin-top: 40px;" :href="apiUrl+'print-pdf/ehp?periode_start='+search.periode_start+'&periode_end='+search.periode_end+'&nama='+search.nama+'&jabatan='+search.jabatan+'&unit='+search.unit+''" target="_BLANK"><i class="fa fa-print fa-sm"></i> Print</a> -->
                </div>
              </div>
              <div class="table-responsive scroll">
                <table class="table align-items-center">
                  <thead>
                    <tr>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">No</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Material Code</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Material Name</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Type</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Unit</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Stock</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Storage Location</th>
                      <th class="text-secondary"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(row, i) in tableMatSeringTerpakai.data" :key="i">
                      <td>
                        <div class="text-center">
                          <h6 class="mb-0 text-sm">{{ i + 1 }}</h6>
                        </div>
                      </td>
                      <td>
                        <div class="text-center">
                          <router-link :to="/detail-barang/+row.material_code">
                            <span class="badge badge-sm bg-gradient-primary">{{ row.material_code }}</span>
                          </router-link>
                        </div>
                      </td>
                      <td>
                        <div class="text-center">
                          <h6 class="mb-0 text-sm">{{ row.material_name }}</h6>
                        </div>
                      </td>
                      <td class="text-sm align-middle">
                        <div class="text-center col">
                          <h6 class="mb-0 text-sm">{{ row.type }}</h6>
                        </div>
                      </td>
                      <td class="text-sm align-middle">
                        <div class="text-center col">
                          <h6 class="mb-0 text-sm">{{ row.unit }}</h6>
                        </div>
                      </td>
                      <td class="text-sm align-middle">
                        <div class="text-center col">
                          <span class="badge badge-sm bg-gradient-success">{{ row.qty }}</span>
                        </div>
                      </td>
                      <td class="text-sm align-middle">
                        <div class="text-center col">
                          <h6 class="mb-0 text-sm">{{ row.storage_location }}</h6>
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
              <label for="example-text-input" class="form-control-label mt-3">Material Code</label>
              <input type="text" class="form-control" placeholder="Material Code" v-model="search.material_code">
              <label for="example-text-input" class="form-control-label mt-3">Material Name</label>
              <input type="text" class="form-control" placeholder="Material Name" v-model="search.material_name" required>
              <label for="example-text-input" class="form-control-label mt-3">Type</label>
              <select class="form-select" aria-label="Default select example" v-model="search.type">
                <option selected>Select Type</option>
                <option value="Consumable">Consumable</option>
                <option value="Sparepart">Sparepart</option>
                <option value="Tools">Tools</option>
                <option value="Other">Other</option>
              </select>
              <label for="example-text-input" class="form-control-label mt-3">Unit</label>
              <select class="form-select" aria-label="Default select example" v-model="search.unit">
                <option selected>Select Unit</option>
                <option value="Batang">Batang</option>
                <option value="Kg">Kg</option>
                <option value="Lembar">Lembar</option>
                <option value="Liter">Liter</option>
                <option value="Meter">Meter</option>
                <option value="M3">M3</option>
                <option value="Pasang">Pasang</option>
                <option value="Pcs">Pcs</option>
                <option value="Roll">Roll</option>
                <option value="Tabung">Tabung</option>
              </select>
              <label for="example-text-input" class="form-control-label mt-3">Storage Location</label>
              <input type="text" class="form-control" placeholder="Storage Location" v-model="search.storage_location" required>
            </div>
            <!-- footer -->
            <div class="row float-right mt-3">
              <div class="col-6"> 
              </div>
              <div class="col-2" style="margin-right: 20px;">
                <argon-button  variant="gradient" color="secondary" size="sm" width="1" @click="formFilter.show = true">Close</argon-button>
              </div>
              <div class="col-1">
                <argon-button variant="gradient" color="success" size="sm" width="1" @click="getMinStock()">Filter</argon-button>
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
// import Card from "@/examples/Cards/Card.vue";
import { VueFinalModal } from 'vue-final-modal'
import ArgonButton from "@/components/ArgonButton.vue";
import Api from '@/helpers/api';
import config from '@/configs/config';
import dashboard from '@/services/dashboard.service';

export default {
  name: "dashboard-default",
  data() {
    return {
      tableMinStock: {
        data: []
      },
      tableMatSeringTerpakai: {
        data: []
      },
      totalItem: '',
      totalBarang: '',
      totalBarangMasuk: '',
      totalBarangKeluar: '',
      formFilter: {
        add: true,
        title: "Filter",
        show: false
      },
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
  components: {
    // Card,
    ArgonButton,
    VueFinalModal,
  },
  mounted(){
    this.get();
    this.getMinStock();
    this.getMatSerTerpakai();
  },
  methods: {
    get(param){
      let context = this;               
      Api(context, dashboard.index()).onSuccess(function(response) {    
          context.totalItem         = response.data.data.totalItem;
          context.totalBarang       = response.data.data.totalBarang;
          context.totalBarangMasuk  = response.data.data.totalBarangMasuk;
          context.totalBarangKeluar = response.data.data.totalBarangKeluar;
      }).onError(function(error) {                    
          if (error.response.status == 404) {
              context.table.data = [];
          }
      }).onFinish(function() { 
         // context.formFilter.show  = false;
      })
      .call()
    },
    getMinStock(param){
      let context = this;               
      Api(context, dashboard.getMinStock({ material_code: context.search.material_code, material_name: context.search.material_name, type: context.search.type, unit: context.search.unit, storage_location: context.search.storage_location })).onSuccess(function(response) {    
          context.tableMinStock.data = response.data.data.data;
      }).onError(function(error) {                    
          if (error.response.status == 404) {
              context.table.data = [];
          }
      }).onFinish(function() { 
         context.formFilter.show  = false;
      })
      .call()
    },
    getMatSerTerpakai(param){
      let context = this;               
      Api(context, dashboard.getMatSerTerpakai({ material_code: context.search.material_code, material_name: context.search.material_name, type: context.search.type, unit: context.search.unit, storage_location: context.search.storage_location })).onSuccess(function(response) {    
          context.tableMatSeringTerpakai.data = response.data.data.data;
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
