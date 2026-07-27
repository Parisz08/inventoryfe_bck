<template>
  <div class="py-4 container-fluid">
    <div class=" row">
      <div class="col-12">
          <div class="card" style="margin-top: -0px;"> 
            <div class="row">
              <div class="col-12">
                <div class="card-header pb-0 text-center">
                  
                  <h6>DETAIL MATERIAL / BARANG</h6>
                  <hr>
                </div>
              </div>
            </div>
            <div class="container">
              <div class="container">
                <div class="row">
                  <div class="col-lg-6 col-sm-12 text-center">
                    <img :src="storageUrl+'image_barang/'+dataBarang.image" class="rounded mb-4 img-fluid" />
                  </div>
                  <div class="col-lg-6 col-sm-12">
                    <p style="margin-top: 10px; font-size: 10px; font-weight: bold;">MATRIAL CODE <span style="margin-left: 35px; margin-right: 10px;">:</span> {{ dataBarang.material_code }}</p>
                    <p style="margin-top: -10px; font-size: 10px; font-weight: bold;">MATERIAL NAME <span style="margin-left: 26px; margin-right: 10px;">:</span> {{ dataBarang.material_name }}</p>
                    <p style="margin-top: -10px; font-size: 10px; font-weight: bold;">SPECIFICATION <span style="margin-left: 35px; margin-right: 10px;">:</span> {{ dataBarang.specification }}</p>
                    <p style="margin-top: -10px; font-size: 10px; font-weight: bold;">TYPE <span style="margin-left: 85px; margin-right: 10px;">:</span> {{ dataBarang.type }}</p>
                    <p style="margin-top: -10px; font-size: 10px; font-weight: bold;">UNIT <span style="margin-left: 84px; margin-right: 10px;">:</span> {{ dataBarang.unit }}</p>
                    <p style="margin-top: -10px; font-size: 10px; font-weight: bold;">STORAGE LOCATION <span style="margin-left: 8px; margin-right: 10px;">:</span> {{ dataBarang.storage_location }}</p>
                    <!-- <p style="margin-top: -10px; font-size: 10px; font-weight: bold;">UNIT PRICE <span style="margin-left: 53px; margin-right: 10px;">:</span> {{ convertRp(dataBarang.unit_price) }}</p> -->
                    <p style="margin-top: -10px; font-size: 15px; font-weight: bold;">STOCK <span style="margin-left: 58px; margin-right: 10px;">:</span> 
                      <span class="badge bg-gradient-success" v-if="dataBarang.stock_barang >= dataBarang.min_stock">{{ dataBarang.stock_barang }}</span>
                      <span class="badge bg-gradient-danger" v-if="dataBarang.stock_barang < dataBarang.min_stock">{{ dataBarang.stock_barang }}</span>
                    </p>
                  </div>
                </div>

                <hr style="margin-top: 0px;">
              </div>
              <div class="card-footer">
              </div>
            </div>
          </div>
      </div>
    </div>
  </div>
</template>

<script>
import Api from '@/helpers/api';
import config from '@/configs/config';
var moment = require('moment');
import barangMasuk from '@/services/barangMasuk.service';

export default {
  name: "tables",
  components: {
    // ArgonButton,
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
      dataBarang: {},
      search: '',
      btnPrintShow: true,
      storageUrl : config.storageUrl,
    };
  },
  mounted(){
    this.get();
    // this.tokenApi = 'Bearer '+localStorage.getItem('token');
    // window.onafterprint = function(){
    //   location.reload()
    // }
  },
  methods: {
    get(){
      let context = this;               
      Api(context, barangMasuk.cekMaterial({material_code: context.$route.params.material_code, material_name: ''})).onSuccess(function(response) {    
          context.dataBarang = response.data.data;
      }).onError(function(error) {                    
          if (error.response.status == 404) {
              context.table.data = [];
          }
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
    print(){
      this.btnPrintShow = false;
      setTimeout(function () {
        window.print();
      },1000); // 5 seconds
    },
    printClose(){
      this.btnPrintShow = true
      alert('ok')
    }
  },
  created() {
    this.$store.state.showSidenav = false;
  },
  beforeMount() {
    // this.$store.state.imageLayout = "profile-overview";
    this.$store.state.showNavbar = false;
    this.$store.state.showFooter = false;
    
    // this.$store.state.hideConfigButton = true;
    // body.classList.add("profile-overview");
  },
  beforeUnmount() {
    // this.$store.state.isAbsolute = false;
    // this.$store.state.imageLayout = "default";
    this.$store.state.showNavbar = true;
    this.$store.state.showFooter = false;
    this.$store.state.showSidenav = true;
    // this.$store.state.hideConfigButton = false;
    // body.classList.remove("profile-overview");
  }

  // created() {
  //   this.$store.state.hideConfigButton = true;
  //   this.$store.state.showNavbar = false;
  //   this.$store.state.showSidenav = false;
  //   this.$store.state.showFooter = false;

  // },
  // beforeUnmount() {
  //   this.$store.state.hideConfigButton = false;
  //   this.$store.state.showNavbar = true;
  //   this.$store.state.showSidenav = true;
  //   this.$store.state.showFooter = true;

  // },
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
