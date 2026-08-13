<template>
  <div class="py-4 container-fluid">
    <div class="row">
      <div class="col-lg-12">
        <div class="row dashboard-stat-row">
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
        </div>
        
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
            </div>
          </div>
          
          <div class="card-body px-0 pt-0 pb-2 mt-4">
            <div class="table-responsive p-0 scroll">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Image</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Material Code</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Material Name</th>
                    <!-- <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Specification</th> -->
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Type</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Unit</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Actual Stock</th>
                    <!-- <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Stock In</th> -->
                    <!-- <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Stock Out</th> -->
                    <!-- <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Min Stock</th> -->
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Storage Location</th>
                    <!-- <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Unit Price</th> -->
                    <!-- <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Created At</th> -->
                    <!-- <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder ">Created By</th> -->
                    <!-- <th class="text-secondary"></th> -->
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(row, i) in table.data" :key="i">
                    <td class="align-middle text-center text-sm">
                      <img
                        :src="storageUrl+'/image_barang/'+row.image"
                        class="avatar avatar-lg me-1"
                        alt="user1"
                      />
                    </td>
                    <td class="align-middle text-center text-sm">
                      <router-link :to="/detail-barang/+row.material_code">
                        <span class="badge badge-sm bg-gradient-primary">{{ row.material_code }}</span>
                      </router-link>
                    </td>
                    <td class="align-middle text-center">
                      <span class="text-secondary text-xs font-weight-bold">{{ row.material_name}}</span>
                    </td>
                   <!--  <td class="align-middle text-center">
                      <span class="text-secondary text-xs font-weight-bold">{{ row.specification }}</span>
                    </td> -->
                    <td class="align-middle text-center">
                      <span class="text-secondary text-xs font-weight-bold">{{ row.type}}</span>
                    </td>
                    <td class="align-middle text-center">
                      <span class="text-secondary text-xs font-weight-bold">{{ row.unit}}</span>
                    </td>
                    <td class="align-middle text-center">
                      <span class="badge bg-gradient-success" v-if="row.stock_barang >= row.min_stock">{{ row.stock_barang }}</span>
                      <span class="badge bg-gradient-danger" v-if="row.stock_barang < row.min_stock">{{ row.stock_barang }}</span>
                    </td>
                   <!--  <td class="align-middle text-center">
                      <span class="text-secondary text-xs font-weight-bold">{{ row.total_barang_masuk_count }}</span>
                    </td>
                    <td class="align-middle text-center">
                      <span class="text-secondary text-xs font-weight-bold">{{ row.total_barang_keluar_count }}</span>
                    </td> -->
                    <!-- <td class="align-middle text-center">
                      <span class="text-secondary text-xs font-weight-bold">{{ row.min_stock }}</span>
                    </td> -->
                    <td class="align-middle text-center">
                      <span class="text-secondary text-xs font-weight-bold">{{ row.storage_location }}</span>
                    </td>
                    <!-- <td class="align-middle text-center">
                      <span class="text-secondary text-xs font-weight-bold">{{ convertRp(row.unit_price) }}</span>
                    </td>
                    <td class="align-middle text-center">
                      <span class="text-secondary text-xs font-weight-bold">{{ moment(row.created_at).locale('id').format('LL') }}</span>
                    </td>
                    <td class="align-middle text-center">
                      <span class="text-secondary text-xs font-weight-bold">{{ row.created_by }}</span>
                    </td> -->
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
          <argon-button variant="gradient" color="success" size="sm" width="1" @click="getStock()">Filter</argon-button>
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
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
var moment = require('moment');
import stockBarang from '@/services/stockBarang.service';

export default {
  name: "dashboard-default",
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
      onLoading: false,
      storageUrl : config.storageUrl,
      dataBarangMasuk: {},
      search: {
        material_code: '',
        material_name: '',
        type: '',
        unit: '',
        storage_location: '',
        date: '',
      },

      totalItem: '',
      totalBarang: '',
      totalBarangMasuk: '',
      totalBarangKeluar: '',
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
    this.getStock();
    this.tokenApi = 'Bearer '+localStorage.getItem('token');
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
    getStock(param){
      let context = this;               
      Api(context, stockBarang.index({material_code: context.search.material_code, material_name: context.search.material_name, type: context.search.type, unit: context.search.unit, storage_location: context.search.storage_location, date: context.search.date})).onSuccess(function(response) {    
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
  font-weight: 500;
}

.dashboard-stat-row::v-deep .numbers p {
  white-space: nowrap;
  font-size: 0.75rem !important;
}
.dashboard-stat-row::v-deep .numbers h5 {
  font-size: 0.85rem;
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
