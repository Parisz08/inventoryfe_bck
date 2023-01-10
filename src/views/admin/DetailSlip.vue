<template>
  <div class="py-4 container-fluid">
    <div class=" row">
      <div class="col-12">
          <div class="card" style="margin-top: -80px;"> 
            <div class="row">
              <div class="col-12">
                <div class="card-header pb-0 text-center">
                  <h6>SLIP GAJI - {{ moment($route.params.periode_start).locale('id').format('MMMM YYYY').toUpperCase() }}</h6><hr>
                </div>
              </div>
            </div>
            <div class="container">
              <div class="container">
                <div class="row">
                  <div class="col-6">
                    <p style="margin-top: 10px; font-size: 15px;">ID <span style="margin-left: 70px; margin-right: 20px;">:</span> {{ slip.id_karyawan }}</p>
                    <p style="margin-top: -10px; font-size: 15px;">NAMA <span style="margin-left: 41px; margin-right: 20px;">:</span> {{ slip.nama }}</p>
                    <p style="margin-top: -10px; font-size: 15px;">JABATAN <span style="margin-left: 23px; margin-right: 20px;">:</span> {{ slip.jabatan }}</p>
                    <p style="margin-top: -10px; font-size: 15px;">STATUS <span style="margin-left: 32px; margin-right: 20px;">:</span> {{ slip.status }}</p>
                    <!-- <hr style="margin-top: 30px;"> -->
                  </div>
                  <div class="col-6 text-center">
                    <img src="../../assets/img/bck.png" width="80" />
                    <p>PT. BUANA CENTRA KARYA</p>
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-6">
                    <hr>
                    PENERIMAAN
                    <hr style="margin-top: 2px;">
                    <p style="font-size: 15px;">Gaji Pokok <span style="margin-left: 63px; margin-right: 10px;">:</span> 
                      {{ convertRp(Math.round(((slip.harian == 0) ? ((slip.bulanan / slip.rel_payroll.periode_total_hk) * slip.total_kerja_count) : (slip.harian * slip.total_kerja_count) ))) }}
                    </p>
                    <p style="margin-top: -10px; font-size: 15px;">Tj. Jabatan / Skill <span style="margin-left: 23px; margin-right: 10px;">:</span>
                      {{ convertRp(slip.tj_jabatan_skill) }}
                    </p>
                    <p style="margin-top: -10px; font-size: 15px;">Transport <span style="margin-left: 67PX; margin-right: 10px;">:</span> 
                      {{ convertRp(slip.transport) }}
                    </p>
                    <p style="margin-top: -10px; font-size: 15px;">U. Makan <span style="margin-left: 70PX; margin-right: 10px;">:</span> 
                      {{ convertRp((slip.makan * slip.total_kerja_count)) }}
                    </p>
                    <p style="margin-top: -10px; font-size: 15px;">Lembur <span style="margin-left: 82PX; margin-right: 10px;">:</span> 
                      {{ convertRp(slip.total_ot_count * ((slip.unit == 'Head Quarter') ? 250000 : 22619)) }}
                    </p>
                    <p style="margin-top: -10px; font-size: 15px;">Jam Lembur <span style="margin-left: 52PX; margin-right: 10px;">:</span> 
                      {{ convertRp(slip.total_ot_count) }}
                    </p>
                    <p style="margin-top: -10px; font-size: 15px;">Bonus <span style="margin-left: 92PX; margin-right: 10px;">:</span> -</p>
                  </div>
                  <div class="col-lg-6">
                    <hr>
                    POTONGAN
                    <hr style="margin-top: 2px;">
                    <p style="font-size: 15px;">JHT <span style="margin-left: 105px; margin-right: 10px;">:</span> 
                      {{ convertRp(slip.upah_bpjs * Number(slip.jht)) }}
                    </p>
                    <p style="margin-top: -10px; font-size: 15px;">JKM <span style="margin-left: 102px; margin-right: 10px;">:</span> 
                      {{ convertRp(slip.upah_bpjs * Number(slip.jkm)) }}
                    </p>
                    <p style="margin-top: -10px; font-size: 15px;">JKK <span style="margin-left: 106px; margin-right: 10px;">:</span> 
                      {{ convertRp(slip.upah_bpjs * Number(slip.jkk)) }}
                    </p>
                    <p style="margin-top: -10px; font-size: 15px;">JP <span style="margin-left: 115px; margin-right: 10px;">:</span> 
                      {{ convertRp(slip.upah_bpjs * Number(slip.jp)) }}
                    </p>
                    <p style="margin-top: -10px; font-size: 15px;">JKS <span style="margin-left: 107px; margin-right: 10px;">:</span> 
                      {{ convertRp(Number(slip.jks) * Number(4309772)) }}
                    </p>
                    <p style="margin-top: -10px; font-size: 15px;">PPH21 <span style="margin-left: 82px; margin-right: 10px;">:</span> -</p>
                    <p style="margin-top: -10px; font-size: 15px;">Lainnya <span style="margin-left: 75px; margin-right: 10px;">:</span> 
                      <span v-if="slip.rel_payroll != null">
                        {{ convertRp(Number(slip.rel_payroll.piutang) + Number(slip.rel_payroll.pinjaman)) }}
                      </span>
                    </p>
                  </div>
                </div>
                <hr style="margin-top: 0px;">
                <div class="row font-weight-bold">
                  <div class="col-6" style="margin-top: -10px;">
                    TOTAL <span style="margin-left: 150px; margin-right: 30px;"></span> {{ convertRp(Math.round(Number(((slip.harian == 0) ? ((slip.bulanan / slip.rel_payroll.periode_total_hk) * slip.total_kerja_count) : (slip.harian * slip.total_kerja_count) ) + slip.tj_jabatan_skill + slip.transport + (slip.makan * slip.total_kerja_count) + slip.total_ot_count * ((slip.unit == 'Head Quarter') ? 250000 : 22619)) )) }}
                    <hr style="margin-top: 2px;">
                  </div>
                  <div class="col-6" style="margin-top: -10px;">
                    <span v-if="slip.rel_payroll != null">
                      TOTAL <span style="margin-left: 150px; margin-right: 30px;"></span> {{ convertRp(Number(slip.upah_bpjs * Number(slip.jht) +  slip.upah_bpjs * Number(slip.jkm) + slip.upah_bpjs * Number(slip.jkk) + slip.upah_bpjs * Number(slip.jp) + Number(slip.jks) * Number(4309772) + (Number(slip.rel_payroll.piutang) + Number(slip.rel_payroll.pinjaman))) ) }}
                    </span>
                    <hr style="margin-top: 2px;">
                  </div>
                </div>
                <div class="row">
                  <div class="col-12 text-center">
                    <span v-if="slip.rel_payroll != null">
                      <p style="margin-top: 30px; font-weight: bold;">THP <span style="margin-left: 65px; "></span> <span style="border-bottom: 3px double;">
                        {{ convertRp(Math.round(Number(((slip.harian == 0) ? ((slip.bulanan / slip.rel_payroll.periode_total_hk) * slip.total_kerja_count) : (slip.harian * slip.total_kerja_count) ) + slip.tj_jabatan_skill + slip.transport + (slip.makan * slip.total_kerja_count) + slip.total_ot_count * ((slip.unit == 'Head Quarter') ? 250000 : 22619))  - ((Number(slip.rel_payroll.piutang) + Number(slip.rel_payroll.pinjaman)) + Number(slip.upah_bpjs * Number(slip.jht) +  slip.upah_bpjs * Number(slip.jkm) + slip.upah_bpjs * Number(slip.jkk) + slip.upah_bpjs * Number(slip.jp) + Number(slip.jks) * Number(4309772))) )) }}
                      </span></p>
                    </span>
                  </div>
                </div>
                    <p style="margin-left: 58px; margin-top: 70px; margin-bottom: 80px;">Payroll</p>
                    <span style="border-bottom: 3px double; margin-left: 35px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                    <hr style="margin-top: 30px;">
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
import dataPayroll from '@/services/dataPayroll.service';
var moment = require('moment');

export default {
  name: "tables",
  components: {
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
      slip: {},
      search: '',
    };
  },
  mounted(){
    this.get();
    this.tokenApi = 'Bearer '+localStorage.getItem('token');
  },
  methods: {
    get(){
      let context = this;               
      Api(context, dataPayroll.show({id_karyawan: context.$route.params.id_karyawan, periode_start: context.$route.params.periode_start, periode_end: context.$route.params.periode_end,})).onSuccess(function(response) {    
          context.slip = response.data.data;
          console.log(response.data.data)
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
