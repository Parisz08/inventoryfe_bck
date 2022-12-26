<template>
  <div class="py-4 container-fluid">
    <div class=" row">
      <div class="col-12">
          <div class="card"> 
            <div class="row">
              <div class="col-12">
                <div class="card-header pb-0 text-center">
                  <h6>SLIP GAJI - NOVEMBER 2022</h6><hr>
                </div>
              </div>
            </div>
            <div class="container">
              <div class="container">
                <div class="row">
                  <div class="col-12">
                    <p style="margin-top: 10px; font-size: 13px;">ID <span style="margin-left: 65px; margin-right: 20px;">:</span> {{ slip.id_karyawan }}</p>
                    <p style="margin-top: -10px; font-size: 13px;">NAMA <span style="margin-left: 39px; margin-right: 20px;">:</span> {{ slip.nama }}</p>
                    <p style="margin-top: -10px; font-size: 13px;">JABATAN <span style="margin-left: 23px; margin-right: 20px;">:</span> {{ slip.jabatan }}</p>
                    <p style="margin-top: -10px; font-size: 13px;">STATUS <span style="margin-left: 30px; margin-right: 20px;">:</span> {{ slip.status }}</p>
                    <!-- <hr style="margin-top: 30px;"> -->
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-6">
                    <hr>
                    PENERIMAAN
                    <hr style="margin-top: 2px;">
                    <p style="font-size: 13px;">Gaji Pokok <span style="margin-left: 58px; margin-right: 10px;">:</span> 
                      {{ convertRp(((slip.harian == 0) ? ((slip.bulanan / 22) * slip.total_kerja_count) : (slip.harian * slip.total_kerja_count) )) }}
                    </p>
                    <p style="margin-top: -10px; font-size: 13px;">Tj. Jabatan / Skill <span style="margin-left: 23px; margin-right: 10px;">:</span>
                      {{ convertRp(slip.tj_jabatan_skill) }}
                    </p>
                    <p style="margin-top: -10px; font-size: 13px;">Transport <span style="margin-left: 62PX; margin-right: 10px;">:</span> 
                      {{ convertRp(slip.transport) }}
                    </p>
                    <p style="margin-top: -10px; font-size: 13px;">U. Makan <span style="margin-left: 64PX; margin-right: 10px;">:</span> 
                      {{ convertRp((slip.makan * slip.total_kerja_count)) }}
                    </p>
                    <p style="margin-top: -10px; font-size: 13px;">Lembur <span style="margin-left: 74PX; margin-right: 10px;">:</span> 
                      {{ convertRp(slip.total_ot_count * ((slip.unit == 'Head Quarter') ? 250000 : 22619)) }}
                    </p>
                    <p style="margin-top: -10px; font-size: 13px;">Jam Lembur <span style="margin-left: 48PX; margin-right: 10px;">:</span> 
                      {{ convertRp(slip.total_ot_count) }}
                    </p>
                    <p style="margin-top: -10px; font-size: 13px;">Bonus <span style="margin-left: 83PX; margin-right: 10px;">:</span> -</p>
                  </div>
                  <div class="col-lg-6">
                    <hr>
                    POTONGAN
                    <hr style="margin-top: 2px;">
                    <p style="font-size: 13px;">JHT <span style="margin-left: 101px; margin-right: 10px;">:</span> 
                      {{ convertRp(slip.upah_bpjs * Number(slip.jht)) }}
                    </p>
                    <p style="margin-top: -10px; font-size: 13px;">JKM <span style="margin-left: 98px; margin-right: 10px;">:</span> 
                      {{ convertRp(slip.upah_bpjs * Number(slip.jkm)) }}
                    </p>
                    <p style="margin-top: -10px; font-size: 13px;">JKK <span style="margin-left: 102px; margin-right: 10px;">:</span> 
                      {{ convertRp(slip.upah_bpjs * Number(slip.jkk)) }}
                    </p>
                    <p style="margin-top: -10px; font-size: 13px;">JP <span style="margin-left: 110px; margin-right: 10px;">:</span> 
                      {{ convertRp(slip.upah_bpjs * Number(slip.jp)) }}
                    </p>
                    <p style="margin-top: -10px; font-size: 13px;">JKS <span style="margin-left: 103px; margin-right: 10px;">:</span> 
                      {{ convertRp(Number(slip.jks) * Number(4309772)) }}
                    </p>
                    <p style="margin-top: -10px; font-size: 13px;">PPH21 <span style="margin-left: 82px; margin-right: 10px;">:</span> -</p>
                    <p style="margin-top: -10px; font-size: 13px;">Lainnya <span style="margin-left: 75px; margin-right: 10px;">:</span> -</p>
                  </div>
                </div>
                <hr style="margin-top: 0px;">
                <div class="row font-weight-bold">
                  <div class="col-6" style="margin-top: -10px;">
                    TOTAL <span style="margin-left: 150px; margin-right: 30px;"></span> {{ convertRp(Number(((slip.harian == 0) ? ((slip.bulanan / 22) * slip.total_kerja_count) : (slip.harian * slip.total_kerja_count) ) + slip.tj_jabatan_skill + slip.transport + (slip.makan * slip.total_kerja_count) + slip.total_ot_count * ((slip.unit == 'Head Quarter') ? 250000 : 22619)) - Number(slip.total_alpa_count)) }}
                    <hr style="margin-top: 2px;">
                  </div>
                  <div class="col-6" style="margin-top: -10px;">
                    TOTAL <span style="margin-left: 150px; margin-right: 30px;"></span> {{ convertRp(Number(slip.upah_bpjs * Number(slip.jht) +  slip.upah_bpjs * Number(slip.jkm) + slip.upah_bpjs * Number(slip.jkk) + slip.upah_bpjs * Number(slip.jp) + Number(slip.jks) * Number(4309772))) }}
                    <hr style="margin-top: 2px;">
                  </div>
                </div>
                <div class="row">
                  <div class="col-12 text-center">
                    <p style="margin-top: 30px; font-weight: bold;">THP <span style="margin-left: 65px; "></span> <span style="border-bottom: 3px double;">
                      {{ convertRp(Number(((slip.harian == 0) ? ((slip.bulanan / 22) * slip.total_kerja_count) : (slip.harian * slip.total_kerja_count) ) + slip.tj_jabatan_skill + slip.transport + (slip.makan * slip.total_kerja_count) + slip.total_ot_count * ((slip.unit == 'Head Quarter') ? 250000 : 22619)) - Number(slip.total_alpa_count) - Number(slip.upah_bpjs * Number(slip.jht) +  slip.upah_bpjs * Number(slip.jkm) + slip.upah_bpjs * Number(slip.jkk) + slip.upah_bpjs * Number(slip.jp) + Number(slip.jks) * Number(4309772)) ) }}
                    </span></p>
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

export default {
  name: "tables",
  components: {
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
      Api(context, dataPayroll.show({id_karyawan: context.$route.params.id_karyawan, periode_start: '2022-11-16', periode_end: '2022-12-15',})).onSuccess(function(response) {    
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
