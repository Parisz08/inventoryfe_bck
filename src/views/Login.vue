<template>
  <div class="container top-0 position-sticky z-index-sticky">
    <div class="row">
      <div class="col-12">
        <!-- <navbar
          isBlur="blur  border-radius-lg my-3 py-2 start-0 end-0 mx-4 shadow"
          v-bind:darkMode="true"
          isBtn="bg-gradient-success"
        /> -->
      </div>
    </div>
  </div>
  <main class="mt-0 main-content">
    <section>
      <div class="page-header min-vh-100">
        <div class="container">
          <div class="row">
            <div class="mx-auto col-xl-4 col-lg-5 col-md-7 d-flex flex-column mx-lg-0">
              <div class="card card-plain">
                <div class="pb-0 card-header text-start">
                  <h4 class="font-weight-bolder">Login</h4>
                  <p class="mb-0">Enter your email and password to Login</p>
                </div>
                <div class="card-body">
                  <!-- <form role="form"> -->
                    <div class="mb-3">
                      <input type="text" class="form-control mb-3" placeholder="Username" v-model="username" v-on:keyup.enter="login()" required>
                    </div>
                    <div class="mb-3">
                      <input type="password" class="form-control mb-3" placeholder="Password" v-model="password" v-on:keyup.enter="login()" required>
                    </div>
                    <argon-switch id="rememberMe">Remember me</argon-switch>

                    <div class="text-center">
                      <argon-button
                        class="mt-4"
                        variant="gradient"
                        color="success"
                        fullWidth
                        size="lg" @click="login()" :disabled="onLoading">
                          <span v-if="onLoading"><i class="fa fa-spinner fa-spin"></i> Please Wait...</span>
                          <span v-else>
                              <span>Login</span>
                          </span>
                        </argon-button>
                    </div>
                    <!-- <div class="text-center">
                      <button type="primary" class="my-4 btn btn-primary btn-outline-dark" @click="login()" :disabled="onLoading">
                          <span v-if="onLoading"><i class="fa fa-spinner fa-spin"></i> Please Wait...</span>
                          <span v-else>
                              <span>Login</span>
                          </span>
                      </button>
                    </div> -->
                  <!-- </form> -->
                </div>
                <div class="px-1 pt-0 text-center card-footer px-lg-2">
                  <p class="mx-auto mb-4 text-sm">
                    <!-- <router-link to="/detail-barang/INVBCK0001" style="color: blue;">
                      Cari material / barang ?. Klik di sini...
                    </router-link> -->
                  </p>
                </div>
              </div>
            </div>
            <div
              class="top-0 my-auto text-center col-6 d-lg-flex d-none h-100 pe-0 position-absolute end-0 justify-content-center flex-column"
            >
              <div
                class="position-relative bg-gradient-primary h-100 m-3 px-7 border-radius-lg d-flex flex-column justify-content-center overflow-hidden bg">
                <span class="mask bg-gradient-dark opacity-7"></span>
                <h4
                  class="mt-5 text-white font-weight-bolder position-relative"
                >"Hi.. Welcome to Inventory BCK"</h4>
                <p
                  class="text-white position-relative"
                >Warehouse management software <br> PT. BUANA CENTRA KARYA</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
</template>

<script>
// import Navbar from "@/examples/PageLayout/Navbar.vue";
// import ArgonInput from "@/components/ArgonInput.vue";
import ArgonSwitch from "@/components/ArgonSwitch.vue";
import ArgonButton from "@/components/ArgonButton.vue";
const body = document.getElementsByTagName("body")[0];
import config from '@/configs/config';
import axios from 'axios';
import Api from '@/helpers/api';
import akun from '@/services/akun.service';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

export default {
  // name: "login",
  components: {
    // Navbar,
    // ArgonInput,
    ArgonSwitch,
    ArgonButton,
  },
  data() {
    return {
      username: '',
      password: '',
      onLoading: false,
    };
  },
  methods: {
    login() {             
      if (this.username && this.password) {
        var formData = new FormData()
        formData.append('username',  this.username)
        formData.append('password', this.password)

        axios.post(config.apiUrl +"auth/login", formData)
        .then(response => {
          localStorage.setItem('token', response.data.access_token)                                        
          localStorage.setItem('authenticated', true) 

            // ======== untuk menentukan admin ============
            let context = this;     
            context.onLoading = true;          
            Api(context, akun.indexProfile()).onSuccess(function(response) {
                var dataRole = response.data.data[0];
                // context.notifyVue('Selamat anda berhasil login '+dataRole.full_name, 'top', 'right', 'info')
                localStorage.setItem('role', dataRole.role)
                localStorage.setItem('username', dataRole.full_name)
                // if (dataRole.role == 'Admin') {
                    context.$router.push('dashboard-default')
                // }else{
                //     context.$router.push('/detail-profile/'+dataRole.employee_id)
                // }
            }).onError(function(error) {  
            })
            .call() 
            // ==============================================                                      
            context.onLoading = true;
        })
        .catch(err => {
         this.notify('Username atau Password Salah', 'error')
        })
      } else {
        this.notify('Error Username Password Required', 'error')
      }
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
  },
  created() {
    this.$store.state.hideConfigButton = true;
    this.$store.state.showNavbar = false;
    this.$store.state.showSidenav = false;
    this.$store.state.showFooter = false;
    body.classList.remove("bg-gray-100");
  },
  beforeUnmount() {
    this.$store.state.hideConfigButton = false;
    this.$store.state.showNavbar = true;
    this.$store.state.showSidenav = true;
    this.$store.state.showFooter = true;
    body.classList.add("bg-gray-100");
  },
};
</script>
<style scoped lang="scss">
.bg {
  background-image: url('../assets/img/randy.jpg');
  background-size: cover;
}
</style>
