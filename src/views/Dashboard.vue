<template>
  <div class="py-4 container-fluid">
    <div class="row">
      <div class="col-lg-12">
        <div class="row">
          <div class="col-lg-3 col-md-6 col-12">
            <card
              title="Total Employees"
              :value="totalKaryawan"
              iconClass="ni ni-single-02"
              iconBackground="bg-gradient-primary"
              directionReverse
            ></card>
          </div>
          <div class="col-lg-3 col-md-6 col-12">
            <card
              title="This Month's Salary"
              :value="convertRp(totalGajiBulanIni)"
              iconClass="fa fa-usd"
              iconBackground="bg-gradient-danger"
              directionReverse
            ></card>
          </div>
          <div class="col-lg-3 col-md-6 col-12">
            <card
              title="Last Month's Salary"
              :value="convertRp(totalGajiBulanLalu)"
              iconClass="ni ni-paper-diploma"
              iconBackground="bg-gradient-success"
              directionReverse
            ></card>
          </div>
          <div class="col-lg-3 col-md-6 col-12">
            <card
              title="Sales"
              value="$103,430"
              iconClass="ni ni-cart"
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
              <div class="p-3 pb-0 card-header">
                <div class="d-flex justify-content-between">
                  <h6 class="mb-2">Employee Hour Performance</h6>
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
      </div>
    </div>
  </div>
</template>
<script>
import Card from "@/examples/Cards/Card.vue";
// import GradientLineChart from "@/examples/Charts/GradientLineChart.vue";
// import Carousel from "./components/Carousel.vue";
// import CategoriesCard from "./components/CategoriesCard.vue";
import Api from '@/helpers/api';
import config from '@/configs/config';
import dashboard from '@/services/dashboard.service';

import US from "@/assets/img/icons/flags/US.png";
import DE from "@/assets/img/icons/flags/DE.png";
import GB from "@/assets/img/icons/flags/GB.png";
import BR from "@/assets/img/icons/flags/BR.png";

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
    };
  },
  components: {
    Card,
    // GradientLineChart,
    // Carousel,
    // CategoriesCard,
  },
  mounted(){
    this.get();
  },
  methods: {
    get(param){
      let context = this;               
      Api(context, dashboard.index()).onSuccess(function(response) {    
          context.table.data         = response.data.data.EHP;
          context.totalKaryawan      = response.data.data.totalKaryawan;
          context.totalGajiBulanIni  = response.data.data.totalGajiBulanIni;
          context.totalGajiBulanLalu = response.data.data.totalGajiBulanLalu;
          console.log(response.data.data.totalGajiBulanIni)
      }).onError(function(error) {                    
          if (error.response.status == 404) {
              context.table.data = [];
          }
      }).onFinish(function() { 
         // context.formFilter.show  = false;
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
  }
};
</script>
