<template>
  <main>
    <div class="container-fluid">
      <div
        class="page-header min-height-300"
        style="
          background-image: url('https://images.unsplash.com/photo-1531512073830-ba890ca4eba2?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80');
          margin-right: -24px;
          margin-left: -34%;
        "
      >
        <span class="mask bg-gradient-success opacity-6"></span>
      </div>
    </div>
    <div class="py-4 container-fluid" style="margin-top: -120px;">
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header pb-0">
              <div class="d-flex align-items-center">
                <p class="mb-0">Edit Profile</p>
                <argon-button color="success" size="sm" class="ms-auto"
                  >Settings</argon-button
                >
              </div>
            </div>
            <div class="card-body">
              <p class="text-uppercase text-sm">Basic Information</p>
              <div class="row">
                <div class="col-md-6">
                  <label for="example-text-input" class="form-control-label mt-3">ID Karyawan</label>
                  <input disabled type="text" class="form-control" @change="updateProfile()" placeholder="ID Karyawan" v-model="karyawan.id_karyawan">
                </div>
                <div class="col-md-6">
                  <label for="example-text-input" class="form-control-label mt-3">Nama Karyawan</label>
                  <input type="text" class="form-control" @change="updateProfile()" placeholder="Nama Karyawan" v-model="karyawan.nama">
                </div>
                <div class="col-md-6">
                  <label for="example-text-input" class="form-control-label mt-3">Jabatan</label>
                  <input type="text" class="form-control" @change="updateProfile()" placeholder="Jabatan" v-model="karyawan.jabatan">
                </div>
                <div class="col-md-6">
                  <label for="example-text-input" class="form-control-label mt-3">Unit</label>
                  <input type="text" class="form-control" @change="updateProfile()" placeholder="Unit" v-model="karyawan.unit">
                </div>
                <div class="col-md-6">
                  <label for="example-text-input" class="form-control-label mt-3">Harian</label>
                  <input type="text" class="form-control" @change="updateProfile()" placeholder="Harian" v-model="karyawan.harian">
                </div>
                <div class="col-md-6">
                  <label for="example-text-input" class="form-control-label mt-3">Bulanan</label>
                  <input type="text" class="form-control" @change="updateProfile()" placeholder="Bulanan" v-model="karyawan.bulanan">
                </div>
                <div class="col-md-6">
                  <label for="example-text-input" class="form-control-label mt-3">TJ. Jabatan / Skill</label>
                  <input type="text" class="form-control" @change="updateProfile()" placeholder="TJ. Jabatan / Skill" v-model="karyawan.tj_jabatan_skill">
                </div>
                <div class="col-md-6">
                  <label for="example-text-input" class="form-control-label mt-3">Transport</label>
                  <input type="text" class="form-control" @change="updateProfile()" placeholder="Transport" v-model="karyawan.transport">
                </div>
                <div class="col-md-6">
                  <label for="example-text-input" class="form-control-label mt-3">Makan</label>
                  <input type="text" class="form-control" @change="updateProfile()" placeholder="Makan" v-model="karyawan.makan">
                </div>
                <div class="col-md-6">
                  <label for="example-text-input" class="form-control-label mt-3">Status</label>
                  <input type="text" class="form-control" @change="updateProfile()" placeholder="Status" v-model="karyawan.status">
                </div>
              </div>

              <hr class="horizontal dark" />
              <p class="text-uppercase text-sm">Rekening Information</p>
              <div class="row">
                <div class="col-md-6">
                  <label for="example-text-input" class="form-control-label mt-3">Nama Bank</label>
                  <input type="text" class="form-control" @change="updateProfile()" placeholder="Nama Bank" v-model="karyawan.bank">
                </div>
                <div class="col-md-6">
                  <label for="example-text-input" class="form-control-label mt-3">No Rekening</label>
                  <input type="text" class="form-control" @change="updateProfile()" placeholder="No Rekening" v-model="karyawan.no_rek">
                </div>
                <div class="col-md-6">
                  <label for="example-text-input" class="form-control-label mt-3">AN Rekening</label>
                  <input type="text" class="form-control" @change="updateProfile()" placeholder="AN Rekening" v-model="karyawan.an_rek">
                </div>
              </div>

              <hr class="horizontal dark" />
              <p class="text-uppercase text-sm">BPJS Information</p>
              <div class="row">
                <div class="col-md-6">
                  <label for="example-text-input" class="form-control-label mt-3">No BPJS TK</label>
                  <input type="text" class="form-control" @change="updateProfile()" placeholder="No BPJS TK" v-model="karyawan.no_bpjs_tk">
                </div>
                <div class="col-md-6">
                  <label for="example-text-input" class="form-control-label mt-3">No BPJS Kes</label>
                  <input type="text" class="form-control" @change="updateProfile()" placeholder="No BPJS Kes" v-model="karyawan.no_bpjs_kes">
                </div>
                <div class="col-md-6">
                  <label for="example-text-input" class="form-control-label mt-3">Upah BPJS</label>
                  <input type="text" class="form-control" @change="updateProfile()" placeholder="Upah BPJS" v-model="karyawan.upah_bpjs">
                </div>
                <div class="col-md-6">
                  <label for="example-text-input" class="form-control-label mt-3">JHT</label>
                  <input type="text" class="form-control" @change="updateProfile()" placeholder="JHT" v-model="karyawan.jht">
                </div>
                <div class="col-md-6">
                  <label for="example-text-input" class="form-control-label mt-3">JKM</label>
                  <input type="text" class="form-control" @change="updateProfile()" placeholder="JKM" v-model="karyawan.jkm">
                </div>
                <div class="col-md-6">
                  <label for="example-text-input" class="form-control-label mt-3">JKK</label>
                  <input type="text" class="form-control" @change="updateProfile()" placeholder="JKK" v-model="karyawan.jkk">
                </div>
                <div class="col-md-6">
                  <label for="example-text-input" class="form-control-label mt-3">JP</label>
                  <input type="text" class="form-control" @change="updateProfile()" placeholder="JP" v-model="karyawan.jp">
                </div>
                <div class="col-md-6">
                  <label for="example-text-input" class="form-control-label mt-3">JKS</label>
                  <input type="text" class="form-control" @change="updateProfile()" placeholder="JKS" v-model="karyawan.jks">
                </div>
              </div>

              <hr class="horizontal dark" />
              <p class="text-uppercase text-sm">Personal Information</p>
              <div class="row">
                <div class="col-md-6">
                  <label for="example-text-input" class="form-control-label mt-3">Nik</label>
                  <input type="text" class="form-control" @change="updateProfile()" placeholder="Nik" v-model="karyawan.nik">
                </div>
                <div class="col-md-6">
                  <label for="example-text-input" class="form-control-label mt-3">No Handphone</label>
                  <input type="text" class="form-control" @change="updateProfile()" placeholder="No Handphone" v-model="karyawan.no_hp">
                </div>
                <div class="col-md-6">
                  <label for="example-text-input" class="form-control-label mt-3">Email</label>
                  <input type="email" class="form-control" @change="updateProfile()" placeholder="Email" v-model="karyawan.email">
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- <div class="col-md-4">
          <profile-card />
        </div> -->
      </div>
    </div>
  </main>
</template>

<script>
import setNavPills from "@/assets/js/nav-pills.js";
import setTooltip from "@/assets/js/tooltip.js";
import ArgonButton from "@/components/ArgonButton.vue";
import Api from '@/helpers/api';
import dataKaryawan from '@/services/dataKaryawan.service';

const body = document.getElementsByTagName("body")[0];

export default {
  name: "profile",
  data() {
    return {
      showMenu: false,
      karyawan: {},
    };
  },
  components: { 
    ArgonButton, 
  },
  mounted() {
    this.get();
    this.$store.state.isAbsolute = true;
    setNavPills();
    setTooltip();
  },
  methods: {
    get() {
      let context = this;               
      Api(context, dataKaryawan.show(context.$route.params.id_karyawan)).onSuccess(function(response) {
          context.karyawan = response.data.data; 
          console.log(response.data.data)              
      })
      .call()        
    },
    updateProfile(){
      let api     = null;
      let context = this;

      api = Api(context, dataKaryawan.update(context.karyawan.id, {
          id_karyawan: context.karyawan.id_karyawan,
          nama: context.karyawan.nama,
          jabatan: context.karyawan.jabatan,
          unit: context.karyawan.unit,
          harian: context.karyawan.harian,
          bulanan: context.karyawan.bulanan,
          transport: context.karyawan.transport,
          makan: context.karyawan.makan,
          tj_jabatan_skill: context.karyawan.tj_jabatan_skill,
          status: context.karyawan.status,

          bank: context.karyawan.bank,
          no_rek: context.karyawan.no_rek,
          an_rek: context.karyawan.an_rek,

          no_bpjs_tk: context.karyawan.no_bpjs_tk,
          no_bpjs_kes: context.karyawan.no_bpjs_kes,
          upah_bpjs: context.karyawan.upah_bpjs,
          jht: context.karyawan.jht,
          jkm: context.karyawan.jkm,
          jkk: context.karyawan.jkk,
          jp: context.karyawan.jp,
          jks: context.karyawan.jks,

          nik: context.karyawan.nik,
          no_hp: context.karyawan.no_hp,
          email: context.karyawan.email,
          total_cuti: context.karyawan.total_cuti,
        }));
      api.onSuccess(function(response) {
          // context.notifyVue(response.data.message, 'top', 'right', 'info')
      }).onError(function(error) { 
          // context.notifyVue('Update Failed', 'top', 'right', 'danger')
      }).onFinish(function() {  
      })
      .call();
    },
  },
  beforeMount() {
    this.$store.state.imageLayout = "profile-overview";
    this.$store.state.showNavbar = false;
    this.$store.state.showFooter = true;
    this.$store.state.hideConfigButton = true;
    body.classList.add("profile-overview");
  },
  beforeUnmount() {
    this.$store.state.isAbsolute = false;
    this.$store.state.imageLayout = "default";
    this.$store.state.showNavbar = true;
    this.$store.state.showFooter = true;
    this.$store.state.hideConfigButton = false;
    body.classList.remove("profile-overview");
  }
};
</script>
